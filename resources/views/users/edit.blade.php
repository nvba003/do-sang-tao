@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-4">Chỉnh Sửa Người Dùng: {{ $user->name }}</h1>

    <form action="{{ route('users.update', $user) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <h3 class="font-bold text-lg mb-2">Vai Trò:</h3>
            @foreach ($roles as $role)
            <div class="flex items-center mb-2">
                <input type="checkbox" id="role{{ $role->id }}" name="roles[]" value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'checked' : '' }} class="h-4 w-4 text-blue-500 rounded">
                <label for="role{{ $role->id }}" class="ml-2 block text-gray-700 text-sm">{{ $role->name }}</label>
            </div>
            @endforeach
        </div>

        <!-- <div class="mb-4">
            <h3 class="font-bold text-lg mb-2">Quyền:</h3>
            @foreach ($permissions as $permission)
            <div class="flex items-center mb-2">
                <input type="checkbox" id="permission{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}" {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }} class="h-4 w-4 text-blue-500 rounded">
                <label for="permission{{ $permission->id }}" class="ml-2 block text-gray-700 text-sm">{{ $permission->name }}</label>
            </div>
            @endforeach
        </div> -->

        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Cập Nhật</button>
    </form>
</div>
@endsection
