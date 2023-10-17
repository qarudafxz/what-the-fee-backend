<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\College;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->string('student_id')->unique();
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('password', 1500);
            $table->string('position', 255);
            $table->string('role', 255);
            $table->boolean('is_verified');
            $table
                ->foreignIdFor(College::class, 'college_id')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
