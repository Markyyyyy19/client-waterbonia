<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public const STATUSES = [
        'received' => 'Received',
        'washing' => 'Washing',
        'drying' => 'Drying',
        'ironing' => 'Ironing',
        'ready_for_pickup' => 'Ready for Pickup',
        'released' => 'Released',
    ];

    protected $fillable = [
        'order_number',
        'customer_id',
        'service_id',
        'weight',
        'price_per_kg',
        'total_amount',
        'discount',
        'status',
        'received_at',
        'expected_pickup_at',
        'released_at',
        'notified',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'received_at' => 'datetime',
        'expected_pickup_at' => 'datetime',
        'released_at' => 'datetime',
        'notified' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = 'WB-'.date('Ymd').'-'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 4));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getAmountPaidAttribute(): float
    {
        return (float) $this->payments->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return round((float) $this->total_amount - $this->amount_paid, 2);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->balance <= 0;
    }
}
