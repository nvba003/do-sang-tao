@extends('layouts.app')

@section('content')
<x-conditional-content :condition="auth()->user()->hasRole('admin')">
<div class="container mx-auto px-4 py-6">
    <a href="{{ route('users.create') }}" class="inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded mb-4">Thêm Người Dùng Mới</a>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border-b text-left">ID</th>
                    <th class="py-2 px-4 border-b text-left">Tên</th>
                    <th class="py-2 px-4 border-b text-left">Email</th>
                    <th class="py-2 px-4 border-b text-left">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr class="bg-white border-b hover:bg-gray-100">
                    <td class="py-2 px-4">{{ $user->id }}</td>
                    <td class="py-2 px-4">{{ $user->name }}</td>
                    <td class="py-2 px-4">{{ $user->email }}</td>
                    <td class="py-2 px-4">
                        <!-- <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-info">Chỉnh Sửa</a> -->
                        <form method="POST" action="{{ route('users.prepareEdit') }}" class="inline-block">
                            @csrf
                            <input type="hidden" name="id" value="{{ $user->id }}">
                            <button type="submit" class="bg-blue-500 text-white font-bold py-1 px-3 rounded">Sửa vai trò</button>
                        </form>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white font-bold py-1 px-3 rounded" onclick="return confirm('Bạn có chắc muốn xoá?')">Xoá</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</x-conditional-content>
@endsection
