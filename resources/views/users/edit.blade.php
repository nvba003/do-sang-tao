{{-- resources/views/users/edit.blade.php --}}

@extends('layouts.app')

@section('content')
    <h1>Chỉnh Sửa Người Dùng: {{ $user->name }}</h1>

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <h3>Vai Trò:</h3>
            @foreach ($roles as $role)
                <div>
                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                           {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                    <label>{{ $role->name }}</label>
                </div>
            @endforeach
        </div>

        <div>
            <h3>Quyền:</h3>
            @foreach ($permissions as $permission)
                <div>
                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                           {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                    <label>{{ $permission->name }}</label>
                </div>
            @endforeach
        </div>

        <button type="submit">Cập Nhật</button>
    </form>
@endsection
