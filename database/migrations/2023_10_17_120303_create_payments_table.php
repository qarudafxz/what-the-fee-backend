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
            $table->decimal('amount', 10, 2);
            $table->date('date');

            $table
                ->string('student_id')
                ->references('student_id')
                ->on('students')
                ->onDelete('cascade');
            $table
                ->string('admin_id')
                ->references('admin_id')
                ->on('admins')
                ->onDelete('cascade');
            $table
                ->foreignIdFor(Semester::class, 'semester_id')
                ->onDelete('cascade');

            $table
                ->string('acad_year')
                ->references('acad_year')
                ->on('semesters')
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
