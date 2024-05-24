@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4" x-data>
    <div class="flex flex-wrap mx-auto mt-2 p-4 bg-white rounded shadow-md mb-4">
        <form id="searchTask" method="GET" class="w-full">
            <div class="flex flex-wrap -mx-2">
                <div class="w-full md:w-1/4 px-2 mb-4">
                    <label for="category_id" class="block text-xs font-bold mb-1">Category:</label>
                    <select id="category_id" name="category_id" class="block w-full shadow border rounded py-2 px-3 leading-tight">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-1/4 px-2 mb-4">
                    <label for="date_from" class="block text-xs font-bold mb-1">From Date:</label>
                    <input type="date" id="date_from" name="date_from" class="block w-full shadow border rounded py-2 px-3 leading-tight">
                </div>
                <div class="w-full md:w-1/4 px-2 mb-4">
                    <label for="date_to" class="block text-xs font-bold mb-1">To Date:</label>
                    <input type="date" id="date_to" name="date_to" class="block w-full shadow border rounded py-2 px-3 leading-tight">
                </div>
                <div class="w-full md:w-1/4 px-2 mb-4 flex items-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded w-full">Filter</button>
                </div>
            </div>
        </form>
        <div class="flex flex-wrap mx-auto items-left" x-data="{ openModal: false }">
            <button @click="openModal = true" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Task
            </button>
            @include('tasks.task_new_modal', ['users' => $users, 'categories' => $categories])
        </div>
    </div>

    <div class="flex flex-wrap -mx-4">
        @foreach ($statuses as $statusKey => $statusName)
            <div class="w-full md:w-1/4 px-4 mb-4">
                <div class="bg-gray-100 p-4 rounded-lg shadow">
                    <h2 class="font-bold text-lg mb-3">{{ $statusName }}</h2>
                    <div class="space-y-4">
                        @foreach ($tasks[$statusKey] ?? [] as $task)
                            <div class="bg-white rounded shadow p-4 mb-4">
                                <h3 class="font-bold">{{ $task->title }}</h3>
                                <p>{{ $task->description }}</p>
                                <a href="{{ route('tasks.show', $task->id) }}" class="text-blue-500 hover:underline">View more</a>
                            </div>
                        @endforeach
                    </div>
                    <button class="mt-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded w-full" onclick="window.location='{{ route('tasks.create') }}'">Add New Task</button>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection


@push('scripts')
<script>
// console.log(@json($categories));
document.addEventListener('alpine:init', () => {
    Alpine.data('taskTable', () => ({
        tasks: @json($tasks),
        init() {
            // Initialize or transform tasks data if needed
        },
    }));
    console.log(tasks);
});
</script>
@endpush
