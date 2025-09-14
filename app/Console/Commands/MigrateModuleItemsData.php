<?php

namespace App\Console\Commands;

use App\Models\Module;
use App\Models\ModuleItem;
use App\Models\QuizQuestion;
use Illuminate\Console\Command;

class MigrateModuleItemsData extends Command
{
    protected $signature = 'migrate:module-items';
    protected $description = 'Migrate existing module items from JSON to new table structure';

    public function handle()
    {
        $modules = Module::whereNotNull('items')->get();
        
        $this->info("Found {$modules->count()} modules with items to migrate");

        foreach ($modules as $module) {
            $this->info("Migrating module: {$module->title}");
            
            foreach ($module->items as $position => $item) {
                $moduleItem = ModuleItem::create([
                    'module_id' => $module->id,
                    'type' => $item['type'],
                    'title' => $item['data']['title'] ?? ucfirst($item['type']),
                    'position' => $position,
                    'data' => $this->prepareItemData($item),
                ]);

                // Handle quiz questions separately
                if ($item['type'] === 'quiz' && !empty($item['data']['questions'])) {
                    foreach ($item['data']['questions'] as $qPosition => $question) {
                        QuizQuestion::create([
                            'module_item_id' => $moduleItem->id,
                            'question_text' => $question['question'] ?? '',
                            'question_type' => $question['type'] ?? 'multiple_choice',
                            'options' => $question['options'] ?? null,
                            'correct_answer' => $question['correct_answer'] ?? null,
                            'points' => $question['points'] ?? 1,
                            'position' => $qPosition,
                        ]);
                    }
                }
            }
        }

        $this->info('Migration completed successfully!');
    }

    private function prepareItemData($item)
    {
        $data = $item['data'] ?? [];
        
        // Remove questions from quiz data since they're now in separate table
        if ($item['type'] === 'quiz') {
            unset($data['questions']);
        }
        
        return $data;
    }
}