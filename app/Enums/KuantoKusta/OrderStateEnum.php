<?php

namespace App\Enums\KuantoKusta;

final class OrderStateEnum
{
    public const string APPROVED = 'approved';

    public const string CANCELED = 'Canceled';

    public const string WAITING_PAYMENT = 'WaitingPayment';

    public const string WAITING_APPROVAL = 'WaitingApproval';

    public const string IN_TRANSIT = 'InTransit';

    public const string DELIVERED = 'delivered';

    public static function getAll(): array
    {
        return [
            self::APPROVED,
            self::CANCELED,
            self::WAITING_PAYMENT,
            self::WAITING_APPROVAL,
            self::IN_TRANSIT,
            self::DELIVERED,
        ];
    }
}
