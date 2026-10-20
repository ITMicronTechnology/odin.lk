<?php

namespace App\Models;

use App\Enums\ServiceInquiryStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'reference', 'service_id', 'user_id', 'name', 'email', 'phone', 'company', 'budget', 'timeline',
    'message', 'status', 'admin_notes',
])]
class ServiceInquiry extends Model
{
    /**
     * Budget choices offered on the request form.
     *
     * @var list<string>
     */
    public const BUDGETS = ['Below Rs. 50,000', 'Rs. 50,000 - 150,000', 'Rs. 150,000 - 500,000', 'Above Rs. 500,000', 'Not sure yet'];

    /**
     * Timeline choices offered on the request form.
     *
     * @var list<string>
     */
    public const TIMELINES = ['As soon as possible', 'Within 1 month', '1 - 3 months', 'Flexible'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ServiceInquiryStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public static function generateReference(): string
    {
        do {
            $reference = 'SRV'.now()->format('ymd').strtoupper(Str::random(4));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }
}
