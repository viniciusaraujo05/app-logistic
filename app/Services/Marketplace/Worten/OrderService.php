<?php

namespace App\Services\Marketplace\Worten;

use App\Actions\Carriers\Tracking\CreateTracking;
use App\Actions\Orders\CheckOrder;
use App\Actions\Orders\CreateOrder;
use App\Actions\Orders\DestroyOrder;
use App\Actions\Orders\UpdateOrder;
use App\Actions\Orders\UpdateOrderStatus;
use App\Enums\FixedStatusesEnum;
use App\Enums\HttpStatusEnum;
use App\Enums\IntegrationEnum;
use App\Integrations\Worten\WortenIntegration;
use App\Models\Order;
use App\Models\Tracking;
use App\Repositories\TokenRepository;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderService
{
    private Collection $tokens;

    private CheckOrder $checkOrder;

    private CreateOrder $createOrder;

    private UpdateOrder $updateOrder;

    private UpdateOrderStatus $updateOrderStatus;

    private DestroyOrder $destroyOrder;

    private CreateTracking $createTracking;

    /**
     * Constructs a new instance of the class.
     *
     */
    public function __construct()
    {
        $tokenRepository = new TokenRepository();
        $this->tokens = $tokenRepository->get(IntegrationEnum::WORTEN);
        $this->checkOrder = new CheckOrder();
        $this->createOrder = new CreateOrder();
        $this->updateOrder = new UpdateOrder();
        $this->updateOrderStatus = new UpdateOrderStatus();
        $this->destroyOrder = new DestroyOrder();
        $this->createTracking = new CreateTracking();
    }

    /**
     * Retrieves a list of orders from the Worten integration.
     *
     * @return array A list of orders retrieved from the Worten integration.
     */
    public function getOrders(): array
    {
        $endpoint = '/orders?' .
            'order_state_codes=WAITING_DEBIT,WAITING_DEBIT_PAYMENT,WAITING_ACCEPTANCE,SHIPPING,SHIPPED,REFUSED,CLOSED&' .
            'paginate=false&&max=50000';

        $orders = [];
        foreach ($this->tokens as $token) {
            $tokenData = json_decode($token, true);
            $token = json_decode($tokenData['token'], true);

            $integration = new WortenIntegration($tokenData['url']);
            $response = $integration->get(
                $token,
                $endpoint,
            );

            if (isset($response['orders'])) {
                $orders[] = $response;
            } else {
                Log::warning('Unexpected response format', ['response' => $response]);
            }
        }

        return $orders;
    }


    /**
     * Retrieves the order by its ID and returns an array of threads associated with the order.
     *
     * @param int $orderId The ID of the order.
     */
    public function getOrderById(int $orderId): array
    {
        $endpoint = "/orders/$orderId/threads";

        $order = [];
        foreach ($this->tokens as $token) {
            $integration = new WortenIntegration($token['url']);
            $order[] = $integration->get(
                $token['token'],
                $endpoint
            );
        }

        return $order;
    }

    /**
     * Syncs the orders by saving them and returns a JSON response.
     *
     * @param mixed $orders The orders to be synced.
     * @return JsonResponse Returns a JSON response with a success message.
     *
     * @throws Exception If an error occurs during the synchronization process.
     * @throws Throwable
     */
    public function syncOrders(mixed $orders): JsonResponse
    {
        try {
            $this->saveOrders($orders);

            return response()->json(
                [
                    'message' => 'Orders synced successfully.',
                ],
                HttpStatusEnum::OK
            );
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return response()->json(['message' => $e->getMessage()], HttpStatusEnum::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Saves the orders to the database.
     *
     * @param array $orders An array of orders to be saved.
     * @throws Throwable
     */
    private function saveOrders(array $orders): void
    {
        $orderDataList = $this->extractOrders($orders);

        foreach ($orderDataList as $orderData) {
            $customerData = $orderData['customer'];
            //remove 'shipped'
            if (in_array($orderData['order_state'], ['REFUSED', 'CLOSED', 'SHIPPED'])) {
                $this->destroyOrder->execute($orderData['order_id']);
                continue;
            }

            if ($orderData['order_state'] === 'SHIPPED') {
                $this->handleShippedOrder($orderData, $customerData);
                continue;
            }

            $this->handleOtherOrderStates($orderData, $customerData);
        }
    }

    private function extractOrders(array $orders): array
    {
        if (empty($orders) || !isset($orders[0]['orders'])) {
            Log::info('Extracting orders failed', [$orders]);
            return [];
        }

        return $orders[0]['orders'];
    }

    /**
     * @throws Throwable
     */
    private function handleShippedOrder(array $orderData, array $customerData): void
    {
        $shippingTracking = $orderData['shipping_tracking'] ?? null;

        if ($shippingTracking) {
            $deliveredStatusId = getOrderStatusIdByName(FixedStatusesEnum::DELIVERED);
            $order = Order::query()
                ->where('order_code', $orderData['order_id'])
                ->where('status_id', '!=', $deliveredStatusId)
                ->first();

            if (!$order) {
                $sentStatusId = getOrderStatusIdByName(FixedStatusesEnum::SENT);
                $orderData['status_id'] = $sentStatusId;
                $order = $this->createOrder($orderData, $customerData);
            }

            $tracking = Tracking::query()->where('order_id', $order->id)->first();

            if (!$tracking) {
                $this->createTracking->execute(
                    [
                        'tracking' => $orderData['shipping_tracking'],
                        'order_code' => $orderData['order_id'],
                        'order_id' => $order->id,
                        'carrier_name' => $orderData['shipping_company'] ?? 'Correos',
                        'integration_id' => getIntegrationIdByName(IntegrationEnum::WORTEN),
                    ]
                );
            }
        }
    }

    /**
     * @throws Throwable
     */
    private function createOrder(array $orderData, array $customerData): JsonResponse
    {
        return $this->createOrder->execute([
            'order_code' => $orderData['order_id'],
            'customer_name' => $customerData['firstname'] . ' ' . $customerData['lastname'],
            'shipping_address' => [
                'street' => $customerData['shipping_address']['street_1'] ?? '',
                'city' => $customerData['shipping_address']['city'] ?? '',
                'state' => $customerData['shipping_address']['state'] ?? '',
                'country' => $customerData['shipping_address']['country'] ?? '',
                'postcode' => $customerData['shipping_address']['zip_code'] ?? '',
            ],
            'email' => $orderData['customer_notification_email'],
            'phone' => $customerData['shipping_address']['phone'] ?? '',
            'notes' => '',
            'status' => $orderData['order_state'],
            'status_id' => $orderData['status_id'],
            'weight' => '',
            'price' => $orderData['total_price'],
            'products' => $this->getProducts($orderData['order_lines']),
            'source_integration_id' => getIntegrationIdByName(IntegrationEnum::WORTEN),
        ]);
    }

    /**
     * Get an array of products from the given items array.
     *
     * @param array $items The array of items to extract products from.
     * @return array The array of products extracted from the items.
     */
    public function getProducts(array $items): array
    {
        $products = [];
        foreach ($items as $item) {
            $products[] = [
                'name' => $item['product_title'],
                'sku' => $item['offer_sku'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
            ];
        }

        return $products;
    }

    /**
     * @throws Throwable
     */
    private function handleOtherOrderStates(array $orderData, array $customerData): void
    {
        $status = $orderData['order_state'] === 'SHIPPING'
            ? $this->getOrderStatus(FixedStatusesEnum::PREPARATION)
            : $this->getOrderStatus(FixedStatusesEnum::TO_PAY);

        $pendingStatus = $this->getOrderStatus(FixedStatusesEnum::TO_PAY);

        if ($this->checkOrder->execute(
            $orderData['order_id'],
            $orderData['customer_notification_email'],
            $status,
            $pendingStatus
        )) {
            $this->updateData($orderData);
        } else {
            $this->createOrder($orderData, $customerData);
        }
    }

    /**
     * Retrieves the order status based on the provided status name.
     *
     * @param string $status The name of the status.
     * @return int The order status.
     */
    public function getOrderStatus(string $status): int
    {
        $orderStatus = $this->updateOrderStatus->getOrderedStatuses()->firstWhere('name_fixed', $status);

        return $orderStatus['order'];
    }

    /**
     * Updates the order data with the provided address information.
     *
     * @param array $orderData The order data containing the order ID and customer information.
     *                        The order data should have the following structure:
     *                        - 'order_id': The ID of the order.
     *                        - 'customer': The customer data containing the shipping address.
     *                          The customer data should have the following structure:
     *                          - 'shipping_address': The shipping address data.
     *                            The shipping address data should have the following structure:
     *                            - 'street_1': The first line of the shipping address.
     *                            - 'city': The city of the shipping address.
     *                            - 'state': The state of the shipping address.
     *                            - 'country': The country of the shipping address.
     *                            - 'zip_code': The postal code of the shipping address.
     *                            - 'phone': The phone number of the shipping address.
     * @return mixed The result of the update operation.
     */
    public function updateData(array $orderData)
    {
        $orderId = Order::query()->where('order_code', $orderData['order_id'])->first()->id;
        $customerData = $orderData['customer'];
        $address = $customerData['shipping_address'] ?? [];

        $address = [
            'shipping_address' => [
                'street' => $address['street_1'] ?? '',
                'city' => $address['city'] ?? '',
                'state' => $address['state'] ?? '',
                'country' => $address['country'] ?? '',
                'postcode' => $address['zip_code'] ?? '',
            ],
            'phone' => $customerData['shipping_address']['phone'] ?? '',
        ];

        return $this->updateOrder->execute(
            $address,
            $orderId
        );
    }
}
