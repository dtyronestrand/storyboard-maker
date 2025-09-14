<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_item_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->string('question_type')->default('multiple_choice'); // multiple_choice, true_false, short_answer, etc.
            $table->json('options')->nullable(); // For multiple choice options
            $table->string('correct_answer')->nullable();
            $table->integer('points')->default(1);
            $table->integer('position')->default(0);
            $table->timestamps();
            
            $table->index(['module_item_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};