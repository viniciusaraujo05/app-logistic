<?php

namespace App\Services\Ecommerce\WooCommerce;

use App\Actions\Orders\CheckOrder;
use App\Actions\Orders\CreateOrder;
use App\Actions\Orders\DestroyOrder;
use App\Actions\Orders\UpdateOrderStatus;
use App\Enums\FixedStatusesEnum;
use App\Enums\HttpStatusEnum;
use App\Enums\IntegrationEnum;
use App\Integrations\WooCommerce\WooCommerceIntegration;
use App\Repositories\TokenRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Throwable;

class OrderService
{
    private Collection $tokens;

    private CheckOrder $checkOrder;

    private CreateOrder $createOrder;

    private UpdateOrderStatus $updateOrderStatus;

    private DestroyOrder $destroyOrder;

    /**
     * Constructs a new instance of the class.
     *
     */
    public function __construct()
    {
        $tokenRepository = new TokenRepository();
        $this->tokens = $tokenRepository->get(IntegrationEnum::WOOCOMERCE);
        $this->checkOrder = new CheckOrder();
        $this->createOrder = new CreateOrder();
        $this->updateOrderStatus = new UpdateOrderStatus();
        $this->destroyOrder = new DestroyOrder();
    }

    /**
     * Retrieves orders from the WooCommerce API based on the provided parameters.
     *
     * @return array
     */
    public function getOrders(): array
    {
        $endpoint = '/wp-json/wc/v3/orders?status=on-hold,processing,cancelled,completed&per_page=100';

        $orders = [];
        foreach ($this->tokens as $token) {
            $apiToken = json_decode($token, true);
            $tokenArray = json_decode($apiToken['token'], true);
            $integration = new WooCommerceIntegration($apiToken['url']);

            $orders = array_merge(
                $orders,
                $integration->get($tokenArray, $endpoint)
            );
        }

        return $orders;
    }

    /**
     * Update the order status for a given order ID using the WooCommerce API tokens.
     *
     * @param int $orderId The ID of the order to update
     */
    public function send(int $orderId): void
    {
        $endpoint = '/wp-json/wc/v3/orders/' . $orderId;
        $data = [
            'status' => 'processing',
        ];

        foreach ($this->tokens as $token) {
            $integration = new WooCommerceIntegration($token['url']);
            $integration->patch(
                $token['token'],
                $endpoint,
                [],
                $data
            );
        }
    }

    /**
     * Syncs the orders by retrieving the Magento orders and saving them.
     *
     * @param array $orderData The order data to be synced.
     * @return JsonResponse Returns a JSON response with a success message.
     * @throws Throwable
     */
    public function syncOrders(array $orderData): JsonResponse
    {
        try {
            $this->saveOrders($orderData);

            return response()->json(
                [
                    'message' => 'Orders synced successfully.',
                ],
                HttpStatusEnum::OK
            );
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], HttpStatusEnum::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Saves the orders to the database.
     *
     * @param array $orders The orders to be saved
     * @throws Throwable
     */
    public function saveOrders(array $orders): void
    {
        foreach ($orders as $orderData) {
            if (
                $orderData['status'] === 'cancelled' ||
                $orderData['status'] === 'completed '
            ) {
                $this->destroyOrder->execute($orderData['id']);
                continue;
            }

            $shipping = $orderData['shipping'];
            $billing = $orderData['billing'];
            $status = $orderData['status'] === 'on-hold'
                ? $this->getOrderStatus(FixedStatusesEnum::TO_PAY)
                : $this->getOrderStatus(FixedStatusesEnum::PREPARATION);

            $pendingStatus = $this->getOrderStatus(FixedStatusesEnum::TO_PAY);
            if ($this->checkOrder->execute($orderData['id'], $billing['email'], $status, $pendingStatus)) {
                continue;
            }

            $this->createOrder->execute(
                [
                    'order_code' => $orderData['id'],
                    'customer_name' => $shipping['first_name'] . ' ' . $shipping['last_name'],
                    'shipping_address' => [
                        'street' => $shipping['address_1'],
                        'city' => $shipping['city'],
                        'state' => $shipping['state'],
                        'country' => $shipping['country'],
                        'postcode' => $shipping['postcode'],
                    ],
                    'email' => $billing['email'],
                    'phone' => $billing['phone'],
                    'notes' => '',
                    'status' => '',
                    'weight' => '',
                    'price' => $orderData['total'],
                    'products' => $this->getProducts($orderData['line_items']),
                    'source_integration_id' => getIntegrationIdByName(IntegrationEnum::WOOCOMERCE),
                    'status_id' => $status,
                ]
            );
        }
    }

    public function getOrderStatus($status)
    {
        $orderStatus = $this->updateOrderStatus->getOrderedStatuses()->firstWhere('name', $status);

        return $orderStatus['order'];
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
                'name' => $item['name'],
                'sku' => $item['sku'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
            ];
        }

        return $products;
    }
}
