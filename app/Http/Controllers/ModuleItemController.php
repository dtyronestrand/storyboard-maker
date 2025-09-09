<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\ModuleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ModuleItemController extends Controller
{
    public function store(Request $request, Module $module)
    {
        $request->validate([
            'type' => 'required|string',
            'data' => 'required|array',
            'data.title' => 'required|string|max:255',
        ]);

        $module->items()->create([
            'type' => $request->type,
            'title' => $request->input('data.title'),
            'content' => json_encode($request->data),
            'order' => $module->items()->count() + 1,
        ]);

        return Redirect::back()->with('success', 'Module item created successfully.');
    }

    public function update(Request $request, ModuleItem $item)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $item->update($request->all());

        return Redirect::back()->with('success', 'Module item updated successfully.');
    }

    public function destroy(ModuleItem $item)
    {
        $item->delete();

        return Redirect::back()->with('success', 'Module item deleted successfully.');
    }

    public function reorder(Request $request, Module $module)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:module_items,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($request->items as $item) {
            ModuleItem::find($item['id'])->update(['order' => $item['order']]);
        }

        return Redirect::back()->with('success', 'Module items reordered successfully.');
    }
}