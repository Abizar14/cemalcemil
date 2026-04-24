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
            $table->string('menu_group')->nullable()->after('name');
            $table->string('selling_unit')->nullable()->after('price');

            $table->index(['menu_group', 'is_active']);
            $table->index(['selling_unit', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['menu_group', 'is_active']);
            $table->dropIndex(['selling_unit', 'is_active']);
            $table->dropColumn(['menu_group', 'selling_unit']);
        });
    }
};
