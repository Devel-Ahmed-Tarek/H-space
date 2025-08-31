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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->foreignId('project_manager_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['Open', 'In Progress', 'Completed'])->default('Open');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->index(['status', 'is_approved']);
            $table->index('project_manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
