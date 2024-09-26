<?php

namespace App\Services\Ecommerce\Magento;

use App\Actions\Orders\CheckOrder;
use App\Actions\Orders\CreateOrder;
use App\Actions\Orders\DestroyOrder;
use App\Actions\Orders\UpdateOrderStatus;
use App\Enums\FixedStatusesEnum;
use App\Enums\HttpStatusEnum;
use App\Enums\IntegrationEnum;
use App\Integrations\Magento\MagentoIntegration;
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
        $this->tokens = $tokenRepository->get(IntegrationEnum::MAGENTO);
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
        $orders = collect($this->tokens)->flatMap(
            function ($token) {
                $endpoints = [
                    'pending' => '/rest/default/V1/orders?' .
                        'searchCriteria[filter_groups][0][filters][0][field]=status' .
                        '&searchCriteria[filter_groups][0][filters][0][value]=pending',
                    'processing' => '/rest/default/V1/orders?' .
                        'searchCriteria[filter_groups][0][filters][0][field]=status' .
                        '&searchCriteria[filter_groups][0][filters][0][value]=processing',
                    'canceled' => '/rest/default/V1/orders?' .
                        'searchCriteria[filter_groups][0][filters][0][field]=status' .
                        '&searchCriteria[filter_groups][0][filters][0][value]=canceled',
                    'complete' => '/rest/default/V1/orders?' .
                        'searchCriteria[filter_groups][0][filters][0][field]=status' .
                        '&searchCriteria[filter_groups][0][filters][0][value]=complete',
                    'closed' => '/rest/default/V1/orders?' .
                        'searchCriteria[filter_groups][0][filters][0][field]=status' .
                        '&searchCriteria[filter_groups][0][filters][0][value]=closed',
                ];
                $integration = new MagentoIntegration($token['url']);

                return collect($endpoints)->flatMap(
                    function ($endpoint) use ($integration, $token) {
                        return $integration->get($token['token'], $endpoint)['items'];
                    }
                );
            }
        );

        return $orders->all();
    }

    /**
     * Update the order status for a given order ID using the WooCommerce API tokens.
     *
     * @param int $orderId The ID of the order to update
     */
    public function send(int $orderId): void
    {
        $endpoint = '/rest/default/V1/orders';
        $data = [
            'entity' => [
                'entity_id' => $orderId,
                'state' => 'prossesing',
                'status' => 'prossesing',
            ],
        ];

        foreach ($this->tokens as $token) {
            $integration = new MagentoIntegration($token['url']);
            $integration->post(
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
     * @return JsonResponse Returns a JSON response with a success message.
     * @throws Throwable
     */
    public function syncOrders($order): JsonResponse
    {
        try {
            $this->saveOrders($order);

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
            $incrementId = $orderData['increment_id'];
            if (
                $orderData['status'] === 'canceled' ||
                $orderData['status'] === 'complete' ||
                $orderData['status'] === 'closed'
            ) {
                $this->destroyOrder->execute($incrementId);
                continue;
            }

            $status = $orderData['status'] === 'pending'
                ? $this->getOrderStatus(FixedStatusesEnum::TO_PAY)
                : $this->getOrderStatus(FixedStatusesEnum::PREPARATION);

            $customerEmail = $orderData['customer_email'];
            $pendingStatus = $this->getOrderStatus(FixedStatusesEnum::TO_PAY);
            if ($this->checkOrder->execute($incrementId, $customerEmail, $status, $pendingStatus)) {
                continue;
            }

            $address = $orderData['extension_attributes']['shipping_assignments'][0]['shipping']['address'];
            $this->createOrder->execute(
                [
                    'order_code' => $incrementId,
                    'customer_name' => $address['firstname'] . ' ' . $address['lastname'],
                    'shipping_address' => [
                        'street' => $address['street'][0],
                        'city' => $address['city'],
                        'state' => $address['region'],
                        'country' => $address['country_id'],
                        'postcode' => $address['postcode'],
                    ],
                    'email' => $orderData['customer_email'],
                    'phone' => $address['telephone'],
                    'notes' => '',
                    'status' => '',
                    'weight' => $orderData['weight'],
                    'price' => $orderData['grand_total'],
                    'products' => $this->getProducts($orderData['items']),
                    'status_id' => $status,
                    'shipping_method' => $orderData['shipping_description'],
                    'source_integration_id' => getIntegrationIdByName(IntegrationEnum::MAGENTO),
                ]
            );
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
     * Get an array of products from the given items array.
     *
     * @param array $items The array of items to extract products from.
     * @return array The array of products extracted from the items.
     */
    public function getProducts(array $items): array
    {
        $products = [];
        foreach ($items as $item) {
            if ($item['product_type'] === 'configurable') {
                continue;
            }

            $products[] = [
                'name' => $item['name'],
                'sku' => $item['sku'],
                'price' => $item['price'],
                'quantity' => $item['qty_ordered'],
            ];
        }

        return $products;
    }
}
