@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Thêm Nhà cung cấp mới</h1>
    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Tên Nhà cung cấp</label>
            <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-4">
            <label for="contact_info" class="block text-gray-700 font-bold mb-2">Thông tin liên hệ</label>
            <textarea name="contact_info" id="contact_info" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
        </div>
        <div class="mb-4">
            <label for="average_rating" class="block text-gray-700 font-bold mb-2">Đánh giá trung bình</label>
            <input type="number" name="average_rating" id="average_rating" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" step="0.01" min="0" max="5">
        </div>
        <div class="mb-4">
            <label for="link" class="block text-gray-700 font-bold mb-2">Link</label>
            <input type="url" name="link" id="link" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
            <label for="supplier_group_id" class="block text-gray-700 font-bold mb-2">Nhóm Nhà cung cấp</label>
            <select name="supplier_group_id" id="supplier_group_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Lưu</button>
    </form>
</div>
@endsection
