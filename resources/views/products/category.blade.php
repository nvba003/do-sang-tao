@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-4">
    <form action="{{ route('categories.store') }}" method="POST" class="mb-4 max-w-lg mx-auto py-2 px-4 bg-white shadow rounded">
        @csrf
        <input type="hidden" name="category_id" id="category_id">
        <div class="mb-0">
            <label for="name" class="block text-gray-700">Tên:</label>
            <input type="text" name="name" id="name" class="border border-gray-300 p-2 w-full" required>
        </div>
        <div class="mb-2">
            <label for="definition_id" class="block text-gray-700">Ký tự định danh mã thùng:</label>
            <input type="text" name="definition_id" id="definition_id" class="border border-gray-300 p-2 w-full" required>
        </div>
        <div class="mb-2">
            <label for="parent_id" class="block text-gray-700">Danh mục cha:</label>
            <select name="parent_id" id="parent_id" class="border border-gray-300 p-2 w-full">
                <option value="">Chọn</option>
                @foreach($categories as $parent)
                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-2">
            <label for="notes" class="block text-gray-700">Ghi chú:</label>
            <textarea name="notes" id="notes" class="border border-gray-300 p-2 w-full" rows="1"></textarea>
        </div>
        <div class="mb-2 flex justify-left">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Lưu</button>
            <button type="reset" class="bg-gray-500 hover:bg-gray-700 text-white font-bold ml-2 py-2 px-4 rounded" onclick="resetForm()">Xóa form</button>
        </div>
    </form>
    <!-- <form action="{{ route('categories.store') }}" method="POST" class="mb-4">
        @csrf
        <input type="hidden" name="category_id" id="category_id">

        <div class="mb-4">
            <label for="name" class="block text-gray-700">Tên:</label>
            <input type="text" name="name" id="name" class="border border-gray-300 p-2 w-full" required>
        </div>

        <div class="mb-4">
            <label for="definition_id" class="block text-gray-700">Ký tự định danh mã thùng:</label>
            <input type="text" name="definition_id" id="definition_id" class="border border-gray-300 p-2 w-full" required>
        </div>

        <div class="mb-4">
            <label for="parent_id" class="block text-gray-700">Danh mục cha:</label>
            <select name="parent_id" id="parent_id" class="border border-gray-300 p-2 w-full">
                <option value="">Chọn</option>
                @foreach($categories as $parent)
                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="notes" class="block text-gray-700">Ghi chú:</label>
            <textarea name="notes" id="notes" class="border border-gray-300 p-2 w-full"></textarea>
        </div>

        <div class="mb-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Lưu</button>
            <button type="reset" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded" onclick="resetForm()">Xóa form</button>
        </div>
    </form> -->

    <table class="min-w-full bg-white h-[75%] overflow-y-auto">
        <thead>
            <tr>
                <th class="py-2">ID</th>
                <th class="py-2">Tên</th>
                <th class="py-2">Ký tự</th>
                <th class="py-2">Danh mục cha</th>
                <th class="py-2">Ghi chú</th>
                <th class="py-2">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                @include('products.partial_category', ['category' => $category, 'level' => 0])
            @endforeach
        </tbody>
    </table>
</div>

<script>
    function editCategory(category) {
        document.getElementById('category_id').value = category.id;
        document.getElementById('name').value = category.name;
        document.getElementById('definition_id').value = category.definition_id;
        document.getElementById('parent_id').value = category.parent_id;
        document.getElementById('notes').value = category.notes;
    }

    function resetForm() {
        document.getElementById('category_id').value = '';
        document.getElementById('name').value = '';
        document.getElementById('definition_id').value = '';
        document.getElementById('parent_id').value = '';
        document.getElementById('notes').value = '';
    }
</script>
@endsection
