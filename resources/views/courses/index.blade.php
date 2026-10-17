<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Courses</h2>
            <a href="{{ route('courses.create') }}" class="text-indigo-600">+ New course</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4">
        @if (session('status'))
            <p class="mb-4 rounded bg-green-100 px-4 py-2">{{ session('status') }}</p>
        @endif

        <div class="bg-white shadow rounded divide-y">
            @forelse ($courses as $course)
                <a href="{{ route('courses.show', $course) }}"
                   class="block px-4 py-3 hover:bg-gray-50">
                    <b>{{ $course->code }}</b> - {{ $course->title }}
                    <span class="text-sm text-gray-500">({{ $course->owner->name }})</span>
                    @unless ($course->is_active)
                        <span class="text-xs text-red-600">inactive</span>
                    @endunless
                </a>
            @empty
                <p class="px-4 py-3 text-gray-500">No courses yet.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
