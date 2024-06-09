@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-4">
    <!-- <form action="{{ route('locations.store') }}" method="POST" class="mb-4">
        @csrf
        <input type="hidden" name="location_id" id="location_id">

        <div class="mb-4">
            <label for="location_name" class="block text-gray-700">Tên vị trí:</label>
            <input type="text" name="location_name" id="location_name" class="border border-gray-300 p-2 w-full" required>
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700">Mô tả:</label>
            <textarea name="description" id="description" class="border border-gray-300 p-2 w-full"></textarea>
        </div>

        <div class="mb-4">
            <label for="parent_id" class="block text-gray-700">Vị trí cha:</label>
            <select name="parent_id" id="parent_id" class="border border-gray-300 p-2 w-full">
                <option value="">Chọn</option>
                @foreach($locations as $parent)
                <option value="{{ $parent->id }}">{{ $parent->location_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Lưu</button>
            <button type="reset" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded" onclick="resetForm()">Xóa form</button>
        </div>
    </form> -->
    <form action="{{ route('locations.store') }}" method="POST" class="mb-4 max-w-lg mx-auto py-2 px-4 bg-white shadow rounded">
        @csrf
        <input type="hidden" name="location_id" id="location_id">
        <div class="mb-2">
            <label for="location_name" class="block text-gray-700">Tên vị trí:</label>
            <input type="text" name="location_name" id="location_name" class="border border-gray-300 p-2 w-full" required>
        </div>
        <div class="mb-2">
            <label for="description" class="block text-gray-700">Mô tả:</label>
            <textarea name="description" id="description" class="border border-gray-300 p-2 w-full" rows="1"></textarea>
        </div>
        <div class="mb-2">
            <label for="parent_id" class="block text-gray-700">Vị trí cha:</label>
            <select name="parent_id" id="parent_id" class="border border-gray-300 p-2 w-full">
                <option value="">Chọn</option>
                @foreach($locations as $parent)
                <option value="{{ $parent->id }}">{{ $parent->location_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-2 flex justify-start">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Lưu</button>
            <button type="reset" class="bg-gray-500 hover:bg-gray-700 text-white font-bold ml-2 py-2 px-4 rounded" onclick="resetForm()">Xóa form</button>
        </div>
    </form>

    <table class="min-w-full bg-white h-[75%] overflow-y-auto">
        <thead>
            <tr>
                <th class="py-2">ID</th>
                <th class="py-2">Tên vị trí</th>
                <th class="py-2">Mô tả</th>
                <th class="py-2">Vị trí cha</th>
                <th class="py-2">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($locations as $location)
                @include('containers.partial_location', ['location' => $location, 'level' => 0])
            @endforeach
        </tbody>
    </table>
</div>

<script>
    function editLocation(location) {
        document.getElementById('location_id').value = location.id;
        document.getElementById('location_name').value = location.location_name;
        document.getElementById('description').value = location.description;
        document.getElementById('parent_id').value = location.parent_id;
    }

    function resetForm() {
        document.getElementById('location_id').value = '';
        document.getElementById('location_name').value = '';
        document.getElementById('description').value = '';
        document.getElementById('parent_id').value = '';
    }
</script>
@endsection
