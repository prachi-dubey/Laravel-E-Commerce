<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Confirmed->value, self::Cancelled->value],
            self::Confirmed => [self::Processing->value, self::Cancelled->value],
            self::Processing => [self::Shipped->value, self::Cancelled->value],
            self::Shipped => [self::Delivered->value],
            self::Delivered, self::Cancelled => [],
        };
    }
}
