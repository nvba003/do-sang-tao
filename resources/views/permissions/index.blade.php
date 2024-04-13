@extends('layouts.app')

@section('content')
    <a href="{{ route('permissions.create') }}">Tạo Quyền Mới</a>
    <ul>
        @foreach ($permissions as $permission)
            <li>{{ $permission->name }}</li>
        @endforeach
    </ul>
@endsection