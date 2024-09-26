<?php

namespace App\Actions\Carriers\Ctt;

use App\Models\Address;

class CreateDataService
{
    public function execute(array $data): array
    {
        $sendAddress = Address::query()->first();
        $receiverPostCode = explode('-', $data['postal_code']);
        $senderPostCode = explode('-', $sendAddress['postal_code']);
        $serviceType = explode(' - ', $data['service_type_id']);

        return [
            'service_type' => trim($serviceType[1]),
            'number_of_volumes' => $data['number_of_volumes'],
            'weight' => $data['total_weight_of_volumes'],
            'order_code' => $data['order_code'],
            'order_id' => $data,
            'sender_name' => $sendAddress['name'],
            'sender_contact_name' => $sendAddress['contact_name'],
            'sender_contact_phone' => $sendAddress['phone_number'],
            'sender_email' => $sendAddress['email'],
            'sender_address_street' => $sendAddress['street'],
            'sender_address_place' => $sendAddress['place'],
            'sender_address_postal_code1' => $senderPostCode[0],
            'sender_address_postal_code2' => $senderPostCode[1],
            'sender_address_city' => $sendAddress['city'],
            'receiver_name' => $data['name'],
            'receiver_contact_name' => $data['contact_name'],
            'receiver_contact_phone' => '+351'.$data['telephone'],
            'receiver_email' => $data['email'],
            'receiver_address_street' => $data['street_1'],
            'receiver_address_city' => $data['city'],
            'receiver_address_postal_code1' => $receiverPostCode[0],
            'receiver_address_postal_code2' => $receiverPostCode[1],
            'receiver_instructions' => $data['info_adicional'],
            'country' => $data['country'],
        ];
    }
}
