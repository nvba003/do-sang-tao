@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-xl font-bold mb-4">Task Detail: {{ $task->title }}</h2>

            <!-- Task Details -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold">Details</h3>
                <p>{{ $task->description }}</p>
                <p>Due Date: {{ $task->due_date ? $task->due_date->toFormattedDateString() : 'None' }}</p>
            </div>

            <!-- Comments -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold">Comments</h3>
                @foreach ($task->comments as $comment)
                    <div class="mt-4">
                        <p class="text-gray-600">{{ $comment->user->name }} ({{ $comment->created_at->diffForHumans() }}):</p>
                        <p>{{ $comment->body }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Attachments -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold">Attachments</h3>
                @foreach ($task->attachments as $attachment)
                    <div class="mt-4">
                        <a href="{{ Storage::url($attachment->path) }}" target="_blank">{{ $attachment->name }}</a>
                    </div>
                @endforeach
            </div>

            <!-- Tags -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold">Tags</h3>
                <div class="flex flex-wrap">
                    @foreach ($task->tags as $tag)
                        <span class="m-1 bg-gray-200 hover:bg-gray-300 rounded-full px-2 font-bold text-sm leading-loose cursor-pointer">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
            
            <!-- Related Orders -->
            @if ($task->orders && $task->orders->count())
                <div class="mb-6">
                    <h3 class="text-lg font-semibold">Related Orders</h3>
                    @foreach ($task->orders as $order)
                        <p>{{ $order->order_number }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Related Products -->
            @if ($task->products && $task->products->count())
                <div class="mb-6">
                    <h3 class="text-lg font-semibold">Related Products</h3>
                    @foreach ($task->products as $product)
                        <p>{{ $product->name }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Related Customers -->
            @if ($task->customers && $task->customers->count())
                <div class="mb-6">
                    <h3 class="text-lg font-semibold">Related Customers</h3>
                    @foreach ($task->customers as $customer)
                        <p>{{ $customer->name }}</p>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
@endsection



@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('taskDetail', () => ({
            open: false,

            toggle() {
                this.open = !this.open;
            }
        }));
    });

</script>
@endpush