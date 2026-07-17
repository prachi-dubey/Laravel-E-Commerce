<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Placed = 'placed';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Dispatched = 'dispatched';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Placed => 'Order Placed',
            self::Confirmed => 'Confirmed',
            self::Processing => 'Processing',
            self::Dispatched => 'Dispatched',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * @return array<string>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Placed => [self::Confirmed->value, self::Cancelled->value],
            self::Confirmed => [self::Processing->value, self::Cancelled->value],
            self::Processing => [self::Dispatched->value, self::Cancelled->value],
            self::Dispatched => [self::Delivered->value],
            self::Delivered, self::Cancelled => [],
        };
    }
}
