<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Fillable([
    'order_number', 'user_id', 'status', 'payment_method', 'payment_status', 'subtotal', 'shipping_fee',
    'discount', 'total', 'customer_name', 'customer_email', 'customer_phone', 'address', 'city',
    'district', 'postal_code', 'notes',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_method' => PaymentMethod::class,
            'subtotal' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return HasMany<OrderStatusHistory, $this>
     */
    public function histories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest('id');
    }

    /**
     * Use the public order number in URLs instead of the auto-increment id.
     */
    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ODN'.now()->format('ymd').strtoupper(Str::random(5));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Record a status change on the order and in its history timeline.
     */
    public function transitionTo(OrderStatus $status, ?string $note = null): void
    {
        $this->update(['status' => $status]);
        $this->histories()->create(['status' => $status->value, 'note' => $note]);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [OrderStatus::Pending, OrderStatus::Confirmed], true);
    }

    /**
     * Cancel the order and return its items to stock, exactly once.
     *
     * The order row is re-read under a lock so concurrent cancellations
     * (customer and admin, or a double-submitted form) cannot restock twice.
     * Returns false when the order was already cancelled, or when
     * $onlyIfCancellable is set and the order has moved past Confirmed.
     */
    public function cancel(?string $note = null, bool $onlyIfCancellable = false): bool
    {
        $cancelled = DB::transaction(function () use ($note, $onlyIfCancellable): bool {
            $order = static::whereKey($this->getKey())->lockForUpdate()->first();

            if ($order === null || $order->status === OrderStatus::Cancelled) {
                return false;
            }

            if ($onlyIfCancellable && ! $order->isCancellable()) {
                return false;
            }

            foreach ($order->items()->with('product')->get() as $item) {
                $item->product?->increment('stock', $item->quantity);
            }

            $order->transitionTo(OrderStatus::Cancelled, $note);

            return true;
        });

        $this->refresh();

        return $cancelled;
    }
}
