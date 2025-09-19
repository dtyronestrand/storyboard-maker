<?php

namespace App\Http\Controllers;

use App\Models\Rubric;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RubricController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return Inertia::render('Rubrics/Index', [
           'rubrics' => Rubric::all(),
       ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
     return Inertia::render('Rubrics/Create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'performance_levels' => 'required|array|min:1',
            'performance_levels.*' => 'string|max:255',
            'criteria' => 'required|array|min:1',
            'criteria.*.name' => 'required|string|max:255',
            'criteria.*.description' => 'required|string',
            'criteria.*.level_descriptions' => 'required|array',
            'criteria.*.level_points' => 'required|array',
        ]);

        Rubric::create([
            'title' => $request->input('title'),
            'performance_levels' => $request->input('performance_levels'),
            'criteria' => $request->input('criteria'),
        ]);

        return redirect()->route('rubrics.index');

    }

    /**
     * Display the specified resource.
     */
    public function show(Rubric $rubric)
    {
        return Inertia::render('Rubrics/Show', compact('rubric'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rubric $rubric)
    {
        return Inertia::render('Rubrics/Edit', compact('rubric'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rubric $rubric)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'performance_levels' => 'required|array|min:1',
            'performance_levels.*' => 'string|max:255',
            'criteria' => 'required|array|min:1',
            'criteria.*.name' => 'required|string|max:255',
            'criteria.*.description' => 'required|string',
            'criteria.*.level_descriptions' => 'required|array',
            'criteria.*.level_points' => 'required|array',
        ]);

        $rubric->update($request->all());

        return redirect()->route('rubrics.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rubric $rubric)
    {
        $rubric->delete();

        return redirect()->route('rubrics.index');
    }
}