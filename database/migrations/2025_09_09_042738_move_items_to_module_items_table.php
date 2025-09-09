<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, move the data
        $modules = DB::table('modules')->whereNotNull('items')->get();

        foreach ($modules as $module) {
            $items = json_decode($module->items, true);

            if (is_array($items)) {
                foreach ($items as $item) {
                    DB::table('module_items')->insert([
                        'title' => $item['title'],
                        'content' => $item['content'] ?? null,
                        'order' => $item['order'],
                        'module_id' => $module->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Then, drop the column
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->json('items')->nullable();
        });
    }
};
