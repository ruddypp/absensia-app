<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_location_id')->nullable()->constrained()->nullOnDelete();
            $table->date('attendance_date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('check_in_photo')->nullable();
            $table->string('check_out_photo')->nullable();
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();
            $table->string('check_in_address')->nullable();
            $table->string('check_out_address')->nullable();
            $table->string('check_in_ip')->nullable();
            $table->string('check_out_ip')->nullable();
            $table->enum('status', ['present', 'late', 'absent', 'leave', 'holiday', 'permission'])->default('present');
            $table->decimal('late_minutes', 8, 2)->default(0);
            $table->decimal('early_minutes', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_corrected')->default(false);
            $table->foreignId('corrected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['employee_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
