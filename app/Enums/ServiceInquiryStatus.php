<?php

namespace App\Enums;

enum ServiceInquiryStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Quoted = 'quoted';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Closed = 'closed';

    /**
     * Human readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Contacted => 'Contacted',
            self::Quoted => 'Quote sent',
            self::InProgress => 'In progress',
            self::Completed => 'Completed',
            self::Closed => 'Closed',
        };
    }

    /**
     * Bootstrap contextual colour used for status badges.
     */
    public function color(): string
    {
        return match ($this) {
            self::New => 'danger',
            self::Contacted => 'info',
            self::Quoted => 'warning',
            self::InProgress => 'primary',
            self::Completed => 'success',
            self::Closed => 'secondary',
        };
    }

    /**
     * What the customer sees about their request.
     */
    public function description(): string
    {
        return match ($this) {
            self::New => 'We have received your request and will contact you within 1 working day.',
            self::Contacted => 'Our team has contacted you to discuss your requirements.',
            self::Quoted => 'We have sent you a quotation. Reply to us to get started.',
            self::InProgress => 'Your project is in progress.',
            self::Completed => 'Your project has been completed. Thank you!',
            self::Closed => 'This request has been closed.',
        };
    }
}
