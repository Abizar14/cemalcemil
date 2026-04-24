<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashierShift extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'opened_at',
        'closed_at',
        'opening_cash',
        'closing_cash_expected',
        'closing_cash_actual',
        'cash_difference',
        'opening_notes',
        'closing_notes',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'opening_cash' => 'decimal:2',
            'closing_cash_expected' => 'decimal:2',
            'closing_cash_actual' => 'decimal:2',
            'cash_difference' => 'decimal:2',
        ];
    }

    /**
     * Determine whether the shift is still open.
     */
    public function isOpen(): bool
    {
        return $this->status === 'open' && $this->closed_at === null;
    }

    /**
     * Get the cashier who owns the shift.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions recorded during the shift.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'shift_id');
    }

    /**
     * Get the cash flows recorded during the shift.
     */
    public function cashFlows(): HasMany
    {
        return $this->hasMany(CashFlow::class, 'shift_id');
    }
};
