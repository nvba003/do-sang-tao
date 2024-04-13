@extends('layouts.app')

@section('content')
    <a href="{{ route('roles.create') }}">Create New Role</a>
    <ul>
        @foreach ($roles as $role)
            <li>{{ $role->name }}</li>
        @endforeach
    </ul>
@endsection