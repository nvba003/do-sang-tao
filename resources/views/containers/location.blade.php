@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-4">
    <form action="{{ route('locations.store') }}" method="POST" class="mb-4 max-w-lg mx-auto py-2 px-4 bg-white shadow rounded">
        @csrf
        <input type="hidden" name="location_id" id="location_id">  
        <!-- Row 1 -->
        <div class="flex">
            <div class="w-1/2 pr-1">
                <label for="location_name" class="block text-gray-700">Tên vị trí:</label>
                <input type="text" name="location_name" id="location_name" class="border border-gray-300 p-2 w-full" required>
            </div>
            <div class="w-1/2 pl-1">
                <label for="description" class="block text-gray-700">Mô tả:</label>
                <textarea name="description" id="description" class="border border-gray-300 p-2 w-full" rows="1"></textarea>
            </div>
        </div>
        <!-- Row 2 -->
        <div class="flex mb-2">
            <div class="w-1/2 pr-1">
                <label for="parent_id" class="block text-gray-700">Vị trí cha:</label>
                <select name="parent_id" id="parent_id" class="border border-gray-300 p-2 w-full">
                    <option value="">Chọn</option>
                    @foreach($locations as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->location_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-1/2 pl-1">
                <label for="branch_id" class="block text-gray-700">Chi nhánh:</label>
                <select name="branch_id" id="branch_id" class="border border-gray-300 p-2 w-full" required>
                    <option value="">Chọn</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- Row 3 -->
        <div class="mb-2 flex justify-start">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Lưu</button>
            <button type="reset" class="bg-gray-500 hover:bg-gray-700 text-white font-bold ml-2 py-2 px-4 rounded" onclick="resetForm(event)">Xóa form</button>
            <label for="default_branch" class="block text-gray-700 ml-4 mr-2 mt-2">Mặc định:</label>
            <select id="default_branch" class="border border-gray-300 p-2" onchange="setDefaultBranch()">
                <option value="">Chọn</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <table class="min-w-full bg-white h-[75%] overflow-y-auto">
        <thead>
            <tr>
                <th class="py-2">ID</th>
                <th class="py-2">Tên vị trí</th>
                <th class="py-2">Mô tả</th>
                <th class="py-2">Vị trí cha</th>
                <th class="py-2">Chi nhánh</th>
                <th class="py-2">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($locations as $location)
                @include('containers.partial_location', ['location' => $location, 'level' => 0])
            @endforeach
        </tbody>
    </table>
</div>

<script>
    function editLocation(location) {
        document.getElementById('location_id').value = location.id;
        document.getElementById('location_name').value = location.location_name;
        document.getElementById('description').value = location.description;
        document.getElementById('branch_id').value = location.branch_id;
        document.getElementById('parent_id').value = location.parent_id;
    }

    function resetForm(event) {
        event.preventDefault();
        document.getElementById('location_id').value = '';
        document.getElementById('location_name').value = '';
        document.getElementById('description').value = '';
        document.getElementById('parent_id').value = '';
    }

    // Đặt chi nhánh mặc định từ sessionStorage khi tải lại trang
    document.addEventListener('DOMContentLoaded', function() {
        var defaultBranch = sessionStorage.getItem('defaultBranch');
        if (defaultBranch) {
            document.getElementById('default_branch').value = defaultBranch;
            document.getElementById('branch_id').value = defaultBranch;
            if (!window.location.search.includes('branch_id')) {
                var url = new URL(window.location.href);
                url.searchParams.set('branch_id', defaultBranch);
                window.location.href = url.toString();
            }
        }
    });

    function setDefaultBranch() {
        var defaultBranch = document.getElementById('default_branch').value;
        sessionStorage.setItem('defaultBranch', defaultBranch); // Lưu vào sessionStorage
        document.getElementById('branch_id').value = defaultBranch;
        // Cập nhật URL với giá trị branch_id mới
        var url = new URL(window.location.href);
        url.searchParams.set('branch_id', defaultBranch);
        window.location.href = url.toString();
    }

</script>
@endsection
