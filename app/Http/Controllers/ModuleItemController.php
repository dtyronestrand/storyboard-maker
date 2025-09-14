<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\ModuleItem;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ModuleItemController extends Controller
{
    public function store(Request $request, Module $module)
    {
        $request->validate([
            'type' => 'required|string',
            'title' => 'required|string',
            'data' => 'array',
        ]);

        $position = ModuleItem::where('module_id', $module->id)->max('position') + 1;

        $moduleItem = ModuleItem::create([
            'module_id' => $module->id,
            'type' => $request->input('type'),
            'title' => $request->input('title'),
            'position' => $position,
            'data' => $request->input('data', []),
        ]);

        return Redirect::back()->with('success', 'Module item created successfully.');
    }

    public function update(Request $request, Module $module)
    {
        $request->validate([
            'items' => 'required|array',
        ]);

        // For backward compatibility, still update JSON
        $module->items = $request->input('items');
        $module->save();

        return Redirect::back()->with('success', 'Module items updated successfully.');
    }

    public function updateItem(Request $request, Module $module, $itemId)
    {
        // Try to find in new structure first (only if itemId is numeric)
        $moduleItem = null;
        if (is_numeric($itemId)) {
            $moduleItem = ModuleItem::where('module_id', $module->id)
                ->where('id', $itemId)
                ->first();
        }

        if ($moduleItem) {
            $moduleItem->update([
                'title' => $request->input('data.title', $moduleItem->title),
                'data' => array_merge($moduleItem->data ?? [], $request->input('data', [])),
            ]);

            // Handle quiz questions if it's a quiz
            if ($moduleItem->type === 'quiz' && $request->has('data.questions')) {
                $moduleItem->quizQuestions()->delete();
                foreach ($request->input('data.questions', []) as $position => $question) {
                    QuizQuestion::create([
                        'module_item_id' => $moduleItem->id,
                        'question_text' => $question['question'] ?? '',
                        'question_type' => $question['type'] ?? 'multiple_choice',
                        'options' => $question['options'] ?? null,
                        'correct_answer' => $question['correct_answer'] ?? null,
                        'points' => $question['points'] ?? 1,
                        'position' => $position,
                    ]);
                }
            }
        } else {
            // Fallback to legacy JSON structure
            $items = $module->items;
            $itemIndex = collect($items)->search(fn($item) => $item['id'] === $itemId);
            
            if ($itemIndex !== false) {
                $items[$itemIndex]['data'] = $request->input('data');
                $module->items = $items;
                $module->save();
            }
        }

        return Redirect::back()->with('success', 'Item updated successfully.');
    }

    public function deleteItem(Module $module, $itemId)
    {
        // Try new structure first (only if itemId is numeric)
        $moduleItem = null;
        if (is_numeric($itemId)) {
            $moduleItem = ModuleItem::where('module_id', $module->id)
                ->where('id', $itemId)
                ->first();
        }

        if ($moduleItem) {
            $moduleItem->delete(); // Cascade will handle quiz questions
        } else {
            // Fallback to legacy structure
            $items = collect($module->items)->filter(fn($item) => $item['id'] !== $itemId)->values()->toArray();
            $module->items = $items;
            $module->save();
        }

        return Redirect::back()->with('success', 'Item deleted successfully.');
    }

    public function destroy(Request $request, Module $module, $itemId)
    {
        return $this->deleteItem($module, $itemId);
    }
}