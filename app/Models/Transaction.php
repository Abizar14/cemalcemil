<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'shift_id',
        'invoice_number',
        'transaction_date',
        'payment_method',
        'subtotal',
        'total_amount',
        'paid_amount',
        'change_amount',
        'payment_status',
        'transaction_status',
        'cancelled_at',
        'cancel_reason',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'subtotal' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Determine whether the transaction has been cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->transaction_status === 'cancelled';
    }

    /**
     * Get the user who created the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the shift that contains the transaction.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'shift_id');
    }

    /**
     * Get the line items for the transaction.
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
