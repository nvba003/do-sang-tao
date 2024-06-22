@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Thêm Sản phẩm mới cho Nhà cung cấp</h1>
    @if (session('success'))
        <div class="bg-green-500 text-white font-bold rounded-t px-4 py-2">
            {{ session('success') }}
        </div>
    @endif
    <form action="{{ route('supplier-products.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="product_api_id" class="block text-gray-700 font-bold mb-2">Chọn Sản phẩm</label>
            <select name="product_api_id" id="product_api_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @foreach($products as $product)
                    <option value="{{ $product->product_api_id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label for="supplier_id" class="block text-gray-700 font-bold mb-2">Chọn Nhà cung cấp</label>
            <select name="supplier_id" id="supplier_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label for="supplier_product_id" class="block text-gray-700 font-bold mb-2">ID Sản phẩm của Nhà cung cấp</label>
            <input type="text" name="supplier_product_id" id="supplier_product_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-4">
            <label for="supplier_product_url" class="block text-gray-700 font-bold mb-2">URL Sản phẩm của Nhà cung cấp</label>
            <input type="url" name="supplier_product_url" id="supplier_product_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-4">
            <label for="available" class="block text-gray-700 font-bold mb-2">Có sẵn</label>
            <select name="available" id="available" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Lưu</button>
    </form>
</div>
@endsection
