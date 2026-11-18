<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logistic_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_code')->unique(); 
            $table->string('title');               
            $table->text('description');          
            $table->date('task_date');             

            $table->foreignId('operator_id')->constrained('users')->onDelete('cascade');

            $table->enum('status', [
                'pending',
                'in_progress',
                'waiting_approval',
                'approved',
                'need_revision'
            ])->default('pending');

            $table->text('operator_report')->nullable();
            $table->text('issue_note')->nullable();
            $table->text('admin_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistic_tasks');
    }
};
