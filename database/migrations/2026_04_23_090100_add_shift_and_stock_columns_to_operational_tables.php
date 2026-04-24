<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('track_stock')->default(false)->after('image_path');
            $table->integer('stock_quantity')->nullable()->after('track_stock');
            $table->integer('stock_alert_threshold')->default(3)->after('stock_quantity');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->after('user_id')->constrained('cashier_shifts')->nullOnDelete();
            $table->index(['shift_id', 'transaction_date']);
        });

        Schema::table('cash_flows', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->after('user_id')->constrained('cashier_shifts')->nullOnDelete();
            $table->index(['shift_id', 'flow_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shift_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shift_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['track_stock', 'stock_quantity', 'stock_alert_threshold']);
        });
    }
};
