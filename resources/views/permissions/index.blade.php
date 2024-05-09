@extends('layouts.app')

@section('content')
<x-conditional-content :condition="auth()->user()->hasRole('admin')">
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Quyền</h1>
            <a href="{{ route('permissions.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Tạo Quyền Mới</a>
        </div>
        <ul class="bg-white shadow-md rounded-lg divide-y divide-gray-200">
            @foreach ($permissions as $permission)
                <li class="px-6 py-4">
                    <div class="text-gray-900 font-medium">{{ $permission->name }}</div>
                </li>
            @endforeach
        </ul>
    </div>
</x-conditional-content>
@endsection
