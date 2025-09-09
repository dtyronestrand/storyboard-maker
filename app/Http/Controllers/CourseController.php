<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;


class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
      $user = $request->user();
      if (!$user) {
          abort(401, 'Unauthorized');
      }
      $courses = Course::where('user_id', $user->id)->with(['modules' => function($query) {
          $query->orderBy('number', 'asc');
      }])->orderBy('number', 'asc')->get();

      return Inertia::render('Dashboard', [
          'courses' => $courses,
      ]);
    
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
    $request->validate([
        'number' => 'required|integer',
        'prefix' => 'required|string|max:10',
        'name' => 'required|string|max:255',
        'objectives' => 'nullable|array',
        'objectives.*' => 'string|max:255',
    ]);

    $course = new Course();
    $course->number = $request->input('number');
    $course->prefix = $request->input('prefix');
    $course->name = $request->input('name');
    $course->objectives = $request->input('objectives', []);
    $course->user_id = $request->user()->id;
    $course->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        // Ensure user can only view their own courses
        if ($course->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $course->load(['modules' => function($query) {
            $query->orderBy('number', 'asc');
        }]);
        
        return Inertia::render('Courses/Show', [
            'course' => $course
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }
}
