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
        Schema::create('requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->string('request_type');
            $table->string('desc');
            $table->string('value_of_request');
            $table->boolean('is_approved')->default(false);
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
        Schema::dropIfExists('requests');
    }
};
