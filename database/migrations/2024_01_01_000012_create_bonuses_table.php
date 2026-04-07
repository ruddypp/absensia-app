<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['performance', 'thr', 'project', 'referral', 'other']);
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->date('bonus_date');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonuses');
    }
};
