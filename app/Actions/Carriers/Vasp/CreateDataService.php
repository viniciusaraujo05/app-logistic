<?php

namespace App\Actions\Carriers\Vasp;

use App\Models\Address;

class CreateDataService
{
    public function execute(array $data): array
    {
        $sendAddress = Address::query()->first();
        $serviceType = explode(' - ', $data['service_type_id']);

        return [
            'service' => [
                'serviceTypeId' => $serviceType[2],
                'serviceFlow' => 0,
                'numberOfVolumes' => intval($data['number_of_volumes']),
                'totalWeightOfVolumes' => intval($data['total_weight_of_volumes']),
                'amount' => '',
                'senderName' => $sendAddress['name'],
                'senderContactName' => $sendAddress['contact_name'],
                'senderContactPhoneNumber' => $sendAddress['phone_number'],
                'senderContactEmail' => $sendAddress['email'],
                'senderAddressStreet' => $sendAddress['street'],
                'senderAddressPlace' => $sendAddress['place'],
                'senderAddressPostalCode' => $sendAddress['postal_code'],
                'senderAddressPostalCodePlace' => $sendAddress['city'],
                'senderAddressCountryCode' => 'PT',
                'receiverName' => $data['name'],
                'receiverContactName' => $data['contact_name'],
                'receiverContactPhoneNumber' => $data['telephone'],
                'receiverContactEmail' => $data['email'],
                'receiverAddressStreet' => $data['street_1'],
                'receiverAddressPlace' => $data['city'],
                'receiverAddressPostalCode' => $data['postal_code'],
                'receiverAddressPostalCodePlace' => $data['city'],
                'receiverAddressCountryCode' => 'PT',
                'receiverFixedInstructions' => $data['info_adicional'],
            ],
        ];
    }
}
