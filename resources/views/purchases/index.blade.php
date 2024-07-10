@extends('layouts.app')

@section('content')
<div class="px-2 sm:px-3 lg:px-4">
    <div id="productTable" class="w-full" x-data="productTable()">
        <div class="flex flex-wrap mt-2 p-4 bg-white rounded shadow-md mb-2">
            <form id="searchProduct" @submit.prevent="submitForm" class="w-full mb-1">
                <div class="flex flex-wrap -mx-2">
                    <!-- Tìm sản phẩm -->
                    <div class="w-full sm:w-1/4 md:w-4/12 xl:w-8/24 px-2 mb-2 md:mb-0">
                        <label for="searchProductCode" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Tìm sản phẩm:</label>
                        <div class="relative">
                            <input type="text" id="searchProductCode" x-model="searchParams.searchProductCode" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập sản phẩm">
                            <div class="absolute inset-y-0 right-0 flex items-center px-1">
                                <button type="button" @click="searchParams.searchProductCode = ''" class="bg-gray-200 hover:bg-gray-300 text-gray-500 text-sm p-2 rounded-r-md">Xóa</button>
                            </div>
                        </div>
                    </div>
                    <!-- Nhóm -->
                    <div class="w-full sm:w-1/4 md:w-3/12 xl:w-4/24 px-2 mb-1 md:mb-0">
                        <label for="group" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Nhóm:</label>
                        <select id="group" x-model="searchParams.group" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            @foreach($productGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Trạng thái -->
                    <div class="w-full sm:w-1/4 md:w-3/12 xl:w-4/24 px-2 mb-1 md:mb-0">
                        <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Trạng thái:</label>
                        <select id="status" x-model="searchParams.status" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            <option value="0">Chưa tạo nhóm</option>
                            <option value="1">Đã tạo nhóm</option>
                        </select>
                    </div>
                    <!-- Nút Tìm -->
                    <div class="w-full sm:w-1/4 md:w-2/12 xl:w-2/24 mt-2 px-2 flex items-end">
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 w-full">Tìm</button>
                    </div>
                </div>
            </form>
        </div>
        @include('purchases.partial_purchase_table')
    </div>
</div>
@endsection

@push('scripts')
<script>
    const urls = {
        baseUrl: "{{ url('/supplier-products') }}",
    };
document.addEventListener('alpine:init', () => {
    Alpine.data('productTable', () => ({
        products: @json($products),
        loading: false,
        skip: 20,
        suppliers: @json($suppliers),
        searchParams: {
            searchProductCode: '',
            group: '',
            status: '',
        },
        newSupplier: {
            supplier_id: '',
            provider: '1688',
            url: '',
            product_id: ''
        },
        init() {
            console.log(this.products);
            this.renderTable();
        },
        renderTable() {
            const container = document.getElementById('example-table');
            new Handsontable(container, {
                data: this.products,
                colHeaders: ['SKU', 'Tên sản phẩm', 'Số lượng', 'Tình trạng', 'Số lượng đặt'],
                columns: [
                    { data: 'sku' },
                    { data: 'name' },
                    { data: 'quantity' },
                    // { data: 'out_of_stock', renderer: (instance, td, row, col, prop, value) => {
                    //     Handsontable.renderers.TextRenderer.apply(this, arguments);
                    //     td.innerText = value ? 'Hết hàng' : 'Còn hàng';
                    // }},
                    // { data: 'order_quantity' }
                ],
                stretchH: 'all',
                width: '100%',
                height: 400,
                rowHeaders: true,
                filters: true,
                dropdownMenu: true,
                licenseKey: 'non-commercial-and-evaluation'
            });
        },
        loadMore() {
            if (this.loading) return;
            this.loading = true;
            fetch(`{{ route('purchase.load-more') }}?skip=${this.skip}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                this.products = this.products.concat(data);
                this.renderTable(); // Cập nhật bảng với dữ liệu mới
                this.skip += 20;
                this.loading = false;
            });
        },
        fetchData(baseUrl) {
            const url = new URL(baseUrl);
            Object.entries(this.searchParams).forEach(([key, value]) => {
                if (value) {
                    url.searchParams.set(key, value);
                }
            });
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
                })
            .then(response => response.json())
            .then(data => {
                this.products = data.products;
                this.renderTable(); // Cập nhật bảng với dữ liệu mới
            });
        },
        submitForm() {
            this.fetchData(urls.baseUrl);
        },
        
    }));
});
</script>

@endpush
