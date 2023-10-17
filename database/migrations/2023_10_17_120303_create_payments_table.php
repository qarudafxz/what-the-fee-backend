<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Student;
use App\Models\Admin;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //refer on the UI
        Schema::create('payments', function (Blueprint $table) {
            $table->string('ar_no')->unique();
            $table->integer('amount');
            $table
                ->foreignIdFor(Student::class, 'student_id')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
