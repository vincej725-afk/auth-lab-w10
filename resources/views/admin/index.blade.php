<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Admin - Users</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4">
        <table class="w-full bg-white shadow rounded text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Courses owned</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">{{ $user->role }}</td>
                        <td class="px-4 py-2">{{ $user->courses_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
