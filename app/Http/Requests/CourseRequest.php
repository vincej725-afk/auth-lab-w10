<?php

namespace App\Http\Requests;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // a Course model when updating, null when creating
        $course = $this->route('course');

        return $course
            ? $this->user()->can('update', $course)
            : $this->user()->can('create', Course::class);
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required', 'string', 'max:20',
                Rule::unique('courses', 'code')->ignore($this->route('course')),
            ],
            'title' => ['required', 'string', 'max:255'],
            'units' => ['required', 'integer', 'between:1,6'],
            'is_active' => ['boolean'],
        ];
    }
}
