<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('owner')->orderBy('code')->get();
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $course = new Course(['units' => 3, 'is_active' => true]);
        return view('courses.form', compact('course'));
    }

    public function store(CourseRequest $request)
    {
        // The owner comes from the logged-in user, NEVER from the form
        $course = $request->user()->courses()->create($request->validated());

        return redirect()->route('courses.show', $course)
            ->with('status', 'Course created.');
    }

    public function show(Course $course)
    {
        $course->load('owner')->loadCount('students');
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        return view('courses.form', compact('course'));
    }

    public function update(CourseRequest $request, Course $course)
    {
        $course->update($request->validated());

        return redirect()->route('courses.show', $course)
            ->with('status', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('courses.index')->with('status', 'Course deleted.');
    }
}
