<!-- @if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-end">
        {{-- Pagination Info -- Only show on large screens --}}
        <div class="hidden sm:block text-sm text-gray-700 leading-5 mx-2 py-2">
            {!! __('Showing') !!}
            <span class="font-medium">{{ $paginator->firstItem() }}</span>
            {!! __('to') !!}
            <span class="font-medium">{{ $paginator->lastItem() }}</span>
            {!! __('of') !!}
            <span class="font-medium">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </div>
        <div class="flex flex-wrap justify-between flex-1 sm:flex-none">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-l-md">
                    <svg class="w-5 h-5 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="sm:hidden">{!! __('pagination.previous') !!}</span>
                </span>
            @else
                <button @click.prevent="fetchData('{{ $paginator->previousPageUrl() }}', searchParams)" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="sm:hidden">{!! __('pagination.previous') !!}</span>
                </button>
            @endif

            {{-- Pagination Elements --}}
            <div class="hidden sm:flex sm:flex-wrap">
                @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-100 border border-blue-300 cursor-default leading-5">
                            {{ $page }}
                        </span>
                    @else
                        <button @click.prevent="fetchData('{{ $url }}', searchParams)" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button @click.prevent="fetchData('{{ $paginator->nextPageUrl() }}', searchParams)" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="sm:hidden">{!! __('pagination.next') !!}</span>
                </button>
            @else
                <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-r-md">
                    <svg class="w-5 h-5 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="sm:hidden">{!! __('pagination.next') !!}</span>
                </span>
            @endif
        </div>
    </nav>
@endif -->

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-end">
        {{-- Pagination Info -- Only show on large screens --}}
        <div class="hidden sm:block text-sm text-gray-700 leading-5 mx-2 py-2">
            {!! __('Showing') !!}
            <span class="font-medium">{{ $paginator->firstItem() }}</span>
            {!! __('to') !!}
            <span class="font-medium">{{ $paginator->lastItem() }}</span>
            {!! __('of') !!}
            <span class="font-medium">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </div>
        <div class="flex flex-wrap justify-between flex-1 sm:flex-none">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-l-md">
                    <svg class="w-5 h-5 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="sm:hidden">{!! __('pagination.previous') !!}</span>
                </span>
            @else
                <button @click.prevent="fetchData('{{ $paginator->previousPageUrl() }}', searchParams)" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="sm:hidden">{!! __('pagination.previous') !!}</span>
                </button>
            @endif

            {{-- Pagination Elements --}}
            <div class="hidden sm:flex sm:flex-wrap">
                @php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                    $visiblePages = [];

                    $visiblePages[] = 1;

                    if ($currentPage <= 5) {
                        for ($i = 2; $i <= min(5, $lastPage); $i++) {
                            $visiblePages[] = $i;
                        }
                    } else {
                        $visiblePages[] = '...';
                    }

                    if ($currentPage > 5 && $currentPage < $lastPage - 1) {
                        $visiblePages[] = $currentPage - 1;
                        $visiblePages[] = $currentPage;
                        $visiblePages[] = $currentPage + 1;
                    }

                    if ($lastPage > 5 && ($currentPage < $lastPage - 1)) {
                        $visiblePages[] = '...';
                    }

                    for ($i = max($lastPage - 1, 5); $i <= $lastPage; $i++) {
                        $visiblePages[] = $i;
                    }
                @endphp

                @foreach ($visiblePages as $page)
                    @if ($page == '...')
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default leading-5">...</span>
                    @elseif ($page == $paginator->currentPage())
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-100 border border-blue-300 cursor-default leading-5">{{ $page }}</span>
                    @else
                        <button @click.prevent="fetchData('{{ $paginator->url($page) }}', searchParams)" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">{{ $page }}</button>
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button @click.prevent="fetchData('{{ $paginator->nextPageUrl() }}', searchParams)" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="sm:hidden">{!! __('pagination.next') !!}</span>
                </button>
            @else
                <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-r-md">
                    <svg class="w-5 h-5 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="sm:hidden">{!! __('pagination.next') !!}</span>
                </span>
            @endif
        </div>
    </nav>
@endif
