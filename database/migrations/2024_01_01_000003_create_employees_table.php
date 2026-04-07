<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->constrained();
            $table->foreignId('position_id')->constrained();
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('nik', 16)->nullable()->unique();
            $table->string('photo')->nullable();
            $table->date('join_date');
            $table->date('resign_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'resigned'])->default('active');
            $table->enum('gender', ['male', 'female']);
            $table->string('address')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
