<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ $course->code }} - {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4">
        @if (session('status'))
            <p class="mb-4 rounded bg-green-100 px-4 py-2">{{ session('status') }}</p>
        @endif

        <div class="bg-white shadow rounded p-6 space-y-2">
            <p><b>Units:</b> {{ $course->units }}</p>
            <p><b>Status:</b> {{ $course->is_active ? 'Active' : 'Inactive' }}</p>
            <p><b>Owner:</b> {{ $course->owner->name }}</p>
            <p><b>Enrolled students:</b> {{ $course->students_count }}</p>

            <div class="flex items-center gap-4 pt-4">
                <a href="{{ route('courses.edit', $course) }}" class="text-indigo-600">Edit</a>

                <form method="POST" action="{{ route('courses.destroy', $course) }}">
                    @csrf
                    @method('DELETE')
                    <x-danger-button>Delete</x-danger-button>
                </form>

                <a href="{{ route('courses.index') }}" class="text-gray-600">Back to list</a>
            </div>
        </div>
    </div>
</x-app-layout>
