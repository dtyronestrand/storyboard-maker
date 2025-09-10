<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ModuleItemController extends Controller
{
    public function update(Request $request, Module $module)
    {
        $request->validate([
            'items' => 'required|array',
        ]);

        $module->items = $request->input('items');
        $module->save();

        return Redirect::back()->with('success', 'Module items updated successfully.');
    }

    public function updateItem(Request $request, Module $module, $itemId)
    {
        $items = $module->items;
        $itemIndex = collect($items)->search(fn($item) => $item['id'] === $itemId);
        
        if ($itemIndex !== false) {
            $items[$itemIndex]['data'] = $request->input('data');
            $module->items = $items;
            $module->save();
        }

        return Redirect::back()->with('success', 'Item updated successfully.');
    }

    public function deleteItem(Module $module, $itemId)
    {
        $items = collect($module->items)->filter(fn($item) => $item['id'] !== $itemId)->values()->toArray();
        $module->items = $items;
        $module->save();

        return Redirect::back()->with('success', 'Item deleted successfully.');
    }

    public function destroy(Request $request, Module $module, $itemId)
    {
        $items = $module->items;
        $itemIndex = collect($items)->search(fn($item) => $item['id'] === $itemId);
        
        if ($itemIndex !== false) {
            array_splice($items, $itemIndex, 1);
            $module->items = $items;
            $module->save();
        }

        return Redirect::back()->with('success', 'Item deleted successfully.');
    }
}