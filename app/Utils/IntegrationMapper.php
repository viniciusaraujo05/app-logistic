<?php

namespace App\Utils;

class IntegrationMapper
{
    /**
     * Map integration names to their corresponding values.
     */
    public function integrationName(string $name): ?string
    {
        $integrationMap = [
            'Magento' => 'magento',
            'WooCommerce' => 'woocommerce',
            'Worten' => 'worten',
            'KuantoKusta' => 'kuantokusta',
            'Vasp' => 'vasp',
            'Envio Próprio' => 'selfshipping',
            'Levantamento em loja' => 'pickup',
            'Ctt' => 'ctt',
            'Correos' => 'correos',
        ];

        return $integrationMap[$name] ?? null;
    }

    /**
     * Map integration values to their corresponding names.
     */
    public function integrationValue(string $value): ?string
    {
        $value = strtolower($value);
        $integrationMap = [
            'magento' => 'Magento',
            'woocommerce' => 'WooCommerce',
            'worten' => 'Worten',
            'kuantokusta' => 'KuantoKusta',
            'vasp' => 'Vasp',
            'selfshipping' => 'Envio Próprio',
            'pickup' => 'Levantamento em loja',
            'ctt' => 'Ctt',
            'correos' => 'Correos',
        ];

        return $integrationMap[$value] ?? null;
    }
}
