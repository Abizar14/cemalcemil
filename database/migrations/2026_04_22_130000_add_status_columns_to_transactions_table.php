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
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('transaction_status', ['completed', 'cancelled'])
                ->default('completed')
                ->after('payment_status');
            $table->timestamp('cancelled_at')->nullable()->after('transaction_status');
            $table->text('cancel_reason')->nullable()->after('cancelled_at');

            $table->index(['transaction_status', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['transaction_status', 'transaction_date']);
            $table->dropColumn([
                'transaction_status',
                'cancelled_at',
                'cancel_reason',
            ]);
        });
    }
};
