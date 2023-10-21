<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Program;
use App\Models\YearLevel;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->string('student_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->decimal('balance', 8, 2)->default(1300.0);
            $table
                ->foreignIdFor(Program::class, 'program_id')
                ->onDelete('cascade');
            $table
                ->foreignIdFor(YearLevel::class, 'year_level_code')
                ->onDelete('cascade');
            //default value of created_at is current timestamp
            //default value of updated_at is null
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
