<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged in!
                    Chúc mừng
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('fetch.products') }}" class="btn btn-primary">Fetch and Store Products</a>
    @if (session('success'))
        <script>
            window.onload = function() {
                // Hiển thị popup
                alert('{{ session('success') }}');
                
                // Tự động ẩn popup sau 3 giây
                setTimeout(function() {
                    // Đây là nơi bạn có thể ẩn popup, ví dụ bằng cách thay đổi style hoặc sử dụng một thư viện UI
                }, 3000);
            }
        </script>
    @endif
    @endsection

</x-app-layout>
