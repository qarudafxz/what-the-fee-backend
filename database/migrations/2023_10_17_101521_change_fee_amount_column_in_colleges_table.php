<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('colleges', function (Blueprint $table) {
            //
            $table
                ->decimal('fee_amount', 8, 2)
                ->default('0.00')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colleges', function (Blueprint $table) {
            //
        });
    }
};
