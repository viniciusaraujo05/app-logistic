<?php

namespace App\Actions\Carriers\SelfShipping;

use App\Enums\IntegrationEnum;
use App\Models\Address;
use App\Models\Order;

class CreateDataService
{
    public const int MIN_VALUE = 0;

    public const int MAX_VALUE = 999999999999;

    public function execute(array $data): array
    {
        $sendAddress = Address::query()->first();
        $order = Order::query()->where('order_code', $data['order_code'])->first();

        $volumes = [];
        for ($i = 1; $i <= $data['number_of_volumes']; $i++) {
            $weight = round($data['total_weight_of_volumes'] / $data['number_of_volumes'], 2);
            $volume = [
                'bar_code' => str_pad(rand($this::MIN_VALUE, $this::MAX_VALUE), 20, '0', STR_PAD_LEFT),
                'weight' => strval($weight),
                'orden' => strval($i),
            ];
            $volumes[] = $volume;
        }

        $integrationId = getIntegrationIdByName(IntegrationEnum::SELFSHIPPING);
        if (array_key_exists('pickup', $data) && $data['pickup']) {
            $integrationId = getIntegrationIdByName(IntegrationEnum::PICKUP);
        }

        return [
            'integration_id' => $integrationId,
            'number_of_volumes' => $data['number_of_volumes'],
            'volumes' => $volumes,
            'car' => $data['car'] ?? '',
            'pickup' => $data['pickup'] ?? '',
            'order_code' => $data['order_code'],
            'order_id' => $order->id,
            'sender_name' => $sendAddress['name'],
            'sender_contact_name' => $sendAddress['contact_name'],
            'sender_contact_phone' => $sendAddress['phone_number'],
            'sender_email' => $sendAddress['email'],
            'sender_address_street' => $sendAddress['street'],
            'sender_address_place' => $sendAddress['place'],
            'sender_address_postal_code' => $sendAddress['postal_code'],
            'sender_address_city' => $sendAddress['city'],
            'tracking' => str_pad(rand($this::MIN_VALUE, $this::MAX_VALUE), 8, '0', STR_PAD_LEFT),
            'receiver_name' => $data['name'],
            'receiver_contact_name' => $data['contact_name'],
            'receiver_contact_phone' => $data['telephone'],
            'receiver_email' => $data['email'],
            'receiver_address_street' => $data['street_1'],
            'receiver_address_city' => $data['city'],
            'receiver_address_postal_code' => $data['postal_code'],
            'receiver_instructions' => $data['info_adicional'],
        ];
    }
}
