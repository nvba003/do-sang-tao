@extends('layouts.app')

@section('content')
<x-conditional-content :condition="auth()->user()->hasRole('admin')">
    <div class="container mx-auto p-4 mt-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Roles</h1>
            <a href="{{ route('roles.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Create New Role</a>
        </div>
        <ul class="bg-white shadow-md rounded-lg divide-y divide-gray-200">
            @foreach ($roles as $role)
                <li class="px-6 py-4">
                    <div class="text-gray-900 font-medium">{{ $role->name }}</div>
                </li>
            @endforeach
        </ul>
    </div>
</x-conditional-content>
@endsection
