<?php

namespace App\Providers;

use App\Models\BoothSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (! Schema::hasTable('booth_settings')) {
                return;
            }

            $setting = BoothSetting::query()->first();

            if (! $setting) {
                return;
            }

            config([
                'booth' => $setting->toBoothConfig(),
            ]);
        } catch (\Throwable) {
            // Keep env-based defaults when the database is unavailable during boot.
        }
    }
}
