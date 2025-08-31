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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['To Do', 'In Progress', 'Done'])->default('To Do');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent'])->default('Medium');
            $table->date('due_date');
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index(['assigned_user_id', 'status']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
