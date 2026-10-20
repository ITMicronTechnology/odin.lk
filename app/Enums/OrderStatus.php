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
     * Human readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Processing => 'Processing',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Customer-facing explanation shown on the order tracking page.
     */
    public function description(): string
    {
        return match ($this) {
            self::Pending => 'We have received your order. Our team will call you shortly to confirm it.',
            self::Confirmed => 'Your order is confirmed and is being prepared.',
            self::Processing => 'Your items are being checked and packed for delivery.',
            self::Shipped => 'Your order is on the way. Colombo deliveries take 1-2 working days, outstation 2-4 days.',
            self::Delivered => 'Your order has been delivered. Thank you for shopping with Odin.lk!',
            self::Cancelled => 'This order was cancelled. Contact us if you have any questions.',
        };
    }

    /**
     * Bootstrap contextual colour used for status badges.
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Confirmed => 'info',
            self::Processing => 'primary',
            self::Shipped => 'secondary',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }

    /**
     * Statuses shown on the customer tracking timeline, in order.
     *
     * @return list<self>
     */
    public static function timeline(): array
    {
        return [self::Pending, self::Confirmed, self::Processing, self::Shipped, self::Delivered];
    }
}
