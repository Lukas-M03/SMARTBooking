<x-layout>
<div class="card">
    <h1 class="h1-dashboard">All Users</h1>
    <p class="text-gray-600 mt-2">Students and advisers are listed separately.</p>
    <p class="mt-4">
        <a href="{{ route('admin.dashboard') }}" class="forgot-link">Back to Admin Dashboard</a>
    </p>
</div>

<div class="card mt-6">
    <h1 class="h1-dashboard mb-4">Students ({{ $students->count() }})</h1>

    @if ($students->isEmpty())
        <p class="text-gray-600">No student users found.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-300">
                        <th class="py-3 pr-4">Name</th>
                        <th class="py-3 pr-4">Email</th>
                        <th class="py-3 pr-4">Student ID</th>
                        <th class="py-3">Created</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr class="border-b border-gray-200">
                            <td class="py-3 pr-4">{{ $student->name }}</td>
                            <td class="py-3 pr-4">{{ $student->email }}</td>
                            <td class="py-3 pr-4">{{ $student->student_id ?? 'N/A' }}</td>
                            <td class="py-3">{{ $student->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="py-3">
                                <form method="POST" action="{{ route('admin.users.delete', $student) }}" onsubmit="return confirm('Delete this student and related records?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="card mt-6">
    <h1 class="h1-dashboard mb-4">Advisers ({{ $advisers->count() }})</h1>

    @if ($advisers->isEmpty())
        <p class="text-gray-600">No adviser users found.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-300">
                        <th class="py-3 pr-4">Name</th>
                        <th class="py-3 pr-4">Email</th>
                        <th class="py-3 pr-4">Phone</th>
                        <th class="py-3">Created</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($advisers as $adviser)
                        <tr class="border-b border-gray-200">
                            <td class="py-3 pr-4">{{ $adviser->name }}</td>
                            <td class="py-3 pr-4">{{ $adviser->email }}</td>
                            <td class="py-3 pr-4">{{ $adviser->phone ?? 'N/A' }}</td>
                            <td class="py-3">{{ $adviser->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="py-3">
                                <form method="POST" action="{{ route('admin.users.delete', $adviser) }}" onsubmit="return confirm('Delete this adviser and related records?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
</x-layout>
