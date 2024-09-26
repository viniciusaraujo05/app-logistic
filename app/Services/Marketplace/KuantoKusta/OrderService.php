<?php

namespace App\Services\Marketplace\KuantoKusta;

use App\Actions\Orders\CheckOrder;
use App\Actions\Orders\CreateOrder;
use App\Enums\HttpStatusEnum;
use App\Enums\IntegrationEnum;
use App\Integrations\KuantoKusta\KuantoKustaIntegration;
use App\Repositories\TokenRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Throwable;

class OrderService
{
    private Collection $tokens;

    private TokenRepository $tokenRepository;

    private CheckOrder $checkOrder;

    private CreateOrder $createOrder;

    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
        $this->tokens = $this->tokenRepository->get(IntegrationEnum::KUANTOKUSTA);
        $this->checkOrder = new CheckOrder();
        $this->createOrder = new CreateOrder();
    }

    public function getOrders($params): array
    {
        $endpoint = '/kms/orders';

        $orders = [];
        foreach ($this->tokens as $token) {
            $integration = new KuantoKustaIntegration($token['url']);
            $orders[] = $integration->get(
                $token['token'],
                $endpoint,
                $params
            );
        }

        return $orders;
    }

    public function syncOrders($orders): JsonResponse
    {
        try {
            $this->saveOrders($orders);

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
     * @throws Throwable
     */
    private function saveOrders($orders): void
    {
        foreach ($orders[0] as $orderData) {
            if ($this->checkOrder->execute($orderData['orderId'])) {
                continue;
            }

            $address = $orderData['deliveryAddress'];

            $this->createOrder->execute(
                [
                    'order_code' => $orderData['orderId'],
                    'customer_name' => $address['customerName'],
                    'shipping_address' => [
                        'street' => $address['address1'],
                        'city' => $address['city'],
                        'state' => '',
                        'country' => $address['country'],
                        'postcode' => $address['zipCode'],
                    ],
                    'email' => '',
                    'phone' => $address['contact'],
                    'notes' => '',
                    'status' => 'pending_payment',
                    'weight' => '',
                    'price' => $orderData['totalPrice'],
                    'products' => $orderData['products'],
                    'integration' => getIntegrationIdByName(IntegrationEnum::KUANTOKUSTA),
                ]
            );
        }
    }

    public function send($orderId, $data): void
    {
        $endpoint = "/kms/orders/$orderId/send";

        foreach ($this->tokens as $token) {
            $integration = new KuantoKustaIntegration($token['url']);
            $integration->patch(
                $token['token'],
                $endpoint,
                [],
                $data
            );
        }
    }
}
