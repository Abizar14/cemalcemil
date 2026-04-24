<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoothSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'address',
        'city',
        'phone',
        'logo_path',
        'receipt_footer',
        'receipt_paper',
    ];

    /**
     * Build a config-friendly booth payload.
     *
     * @return array<string, string>
     */
    public function toBoothConfig(): array
    {
        $defaults = config('booth');

        return [
            'name' => (string) ($this->name ?: $defaults['name']),
            'address' => (string) ($this->address ?: $defaults['address']),
            'city' => (string) ($this->city ?: $defaults['city']),
            'phone' => (string) ($this->phone ?: $defaults['phone']),
            'logo' => (string) ($this->logo_path ?: $defaults['logo']),
            'receipt_footer' => (string) ($this->receipt_footer ?: $defaults['receipt_footer']),
            'receipt_paper' => (string) ($this->receipt_paper ?: $defaults['receipt_paper']),
        ];
    }
}
