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
        Schema::create('logs', function (Blueprint $table) {
            $table->id('logs_id');
            $table->string('label')->max(50);
            $table->string('method')->max(10);
            $table
                ->string('admin_id')
                ->references('admin_id')
                ->on('admins')
                ->onDelete('cascade');
            $table
                ->string('ar_no')
                ->references('ar_no')
                ->on('payments')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
