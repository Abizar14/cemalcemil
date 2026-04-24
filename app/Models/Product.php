<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'menu_group',
        'price',
        'selling_unit',
        'image_path',
        'track_stock',
        'stock_quantity',
        'stock_alert_threshold',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'track_stock' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the category for the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the transaction details for the product.
     */
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Determine whether the product stock is low.
     */
    public function isLowStock(): bool
    {
        if (! $this->track_stock) {
            return false;
        }

        return (int) ($this->stock_quantity ?? 0) <= (int) $this->stock_alert_threshold;
    }

    /**
     * Determine whether the tracked stock is empty.
     */
    public function isOutOfStock(): bool
    {
        if (! $this->track_stock) {
            return false;
        }

        return (int) ($this->stock_quantity ?? 0) <= 0;
    }

    /**
     * Get the full image URL for the product.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(function (): string {
            $path = $this->image_path;

            if (is_string($path) && $path !== '') {
                if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                    return $path;
                }

                return asset($path);
            }

            return asset('images/products/placeholder.svg');
        });
    }
}
