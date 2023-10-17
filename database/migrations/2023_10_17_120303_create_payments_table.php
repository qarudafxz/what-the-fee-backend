<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Student;
use App\Models\Admin;
use App\Models\Semester;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //refer on the UI
        Schema::create('payments', function (Blueprint $table) {
            $table->string('ar_no')->unique();
            $table->string('desc')->nullable();
            $table->integer('amount');
            $table->date('date');
            $table
                ->foreignIdFor(Student::class, 'student_id')
                ->onDelete('cascade');
            $table->foreignIdFor(Admin::class, 'admin_id')->onDelete('cascade');
            $table
                ->foreignIdFor(Semester::class, 'semester_id')
                ->onDelete('cascade');
            $table
                ->foreignIdFor(Semester::class, 'acad_year')
                ->onDelete('cascade');
            $table->timestamps();
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
