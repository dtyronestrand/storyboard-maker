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
}