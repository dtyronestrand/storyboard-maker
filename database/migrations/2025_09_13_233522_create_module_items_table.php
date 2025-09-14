<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Module;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Module::class)->constrained()->onDelete('cascade');
            $table->string('type'); // overview, page, assignment, quiz, etc.
            $table->string('title');
            $table->integer('position')->default(0);
            $table->json('data')->nullable(); // For simple type-specific data
            $table->timestamps();
            
            $table->index(['module_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_items');
    }
};