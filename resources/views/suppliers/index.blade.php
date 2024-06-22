@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Danh sách Nhà cung cấp</h1>
    <a href="{{ route('suppliers.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Thêm Nhà cung cấp mới</a>
    <table class="min-w-full bg-white shadow-md rounded mt-4">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Tên Nhà cung cấp</th>
                <th class="py-2 px-4 border-b">Thông tin liên hệ</th>
                <th class="py-2 px-4 border-b">Đánh giá trung bình</th>
                <th class="py-2 px-4 border-b">Link</th>
                <th class="py-2 px-4 border-b">Nhóm</th>
                <th class="py-2 px-4 border-b">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $supplier)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $supplier->name }}</td>
                    <td class="py-2 px-4 border-b">{{ $supplier->contact_info }}</td>
                    <td class="py-2 px-4 border-b">{{ $supplier->average_rating }}</td>
                    <td class="py-2 px-4 border-b"><a href="{{ $supplier->link }}" class="text-blue-500" target="_blank">Link</a></td>
                    <td class="py-2 px-4 border-b">{{ $supplier->group->name }}</td>
                    <td class="py-2 px-4 border-b">
                        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded">Chỉnh sửa</a>
                        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
