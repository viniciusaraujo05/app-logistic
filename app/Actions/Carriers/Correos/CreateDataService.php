<?php

namespace App\Actions\Carriers\Correos;

use App\Enums\IntegrationEnum;
use App\Models\Address;
use App\Repositories\TokenRepository;

class CreateDataService
{
    public function execute(array $data): array
    {
        $sendAddress = Address::query()->first();
        $tokenRepository = new TokenRepository();
        $token = $tokenRepository->get(IntegrationEnum::CORREOS)->first();

        $serviceType = explode(' - ', $data['service_type_id']);
        $requester = json_decode($token->token)->requester;
        $labelTypeToLabelMap = [
            'PDF' => '1',
            'ZPL' => '2',
        ];
        $label = $labelTypeToLabelMap[$data['label_type']] ?? '';

        $volumes = [];
        for ($i = 1; $i <= $data['number_of_volumes']; $i++) {
            $volume = [
                'alto' => '',
                'ancho' => '',
                'codBultoCli' => '',
                'codUnico' => '',
                'descripcion' => '',
                'kilos' => number_format($data['total_weight_of_volumes'] / $data['number_of_volumes'], 3),
                'largo' => '',
                'observaciones' => '',
                'orden' => strval($i),
                'referencia' => '',
                'volumen' => '',
            ];
            array_push($volumes, $volume);
        }

        return [
            'solicitante' => $requester,
            'fecha' => date('d-m-Y'),
            'codRte' => $requester,
            'nomRte' => $sendAddress['name'],
            'dirRte' => $sendAddress['street'],
            'pobRte' => $sendAddress['city'],
            'codPosNacRte' => substr($sendAddress['postal_code'], 0, 4),
            'paisISORte' => 'PT',
            'codPosIntRte' => $sendAddress['postal_code'],
            'telefRte' => $sendAddress['phone_number'],
            'nomDest' => $data['name'],
            'dirDest' => $data['street_1'],
            'pobDest' => $data['city'],
            'codPosNacDest' => substr($sendAddress['postal_code'], 0, 4),
            'paisISODest' => 'PT',
            'codPosIntDest' => $data['postal_code'],
            'contacDest' => $data['contact_name'],
            'telefDest' => $data['telephone'],
            'observac' => $data['info_adicional'],
            'numBultos' => $data['number_of_volumes'],
            'kilos' => $data['total_weight_of_volumes'],
            'producto' => $serviceType[0],
            'portes' => 'P',
            'listaBultos' => $volumes,
            'listaInformacionAdicional' => [
                ['tipoEtiqueta' => $label],
            ],
        ];
    }
}
