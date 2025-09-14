<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\ModuleItem;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'number' => 'required|integer',
            'objectives' => 'nullable|array',
            'objectives.*.objective' => 'required|string',
            'objectives.*.aligned_CLOs' => 'nullable|array',
            'course_id' => 'required|integer',
        ]);

        Module::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
      $validated = $request->validate([
            'id' => 'required|integer',
            'title' => 'required|string|max:255',
            'number' => 'required|integer',
            'objectives' => 'nullable|array',
            'items' => 'nullable|array',
        ]);

        $module->update($validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        Module::destroy($module->id);
    }
}
