<?php

namespace App\Enums\Order;

final class OrderStatusTypeEnum
{
    public const int PREPARATION = 1;

    public const int TRACKING = 2;

    private static array $statusTypes = [
        self::PREPARATION => 'preparation',
        self::TRACKING => 'tracking',
    ];

    public static function all(): array
    {
        return self::$statusTypes;
    }

    public static function validationString(): string
    {
        return implode(',', self::$statusTypes);
    }

    public static function getName(int $statusTypeId): ?string
    {
        return self::$statusTypes[$statusTypeId] ?? null;
    }

    public static function getId(string $statusTypeName): ?int
    {
        $statusTypeId = array_search($statusTypeName, self::$statusTypes, true);

        return $statusTypeId !== false ? $statusTypeId : null;
    }
}
