@extends('layouts.app')

@section('content')
<div class="container mx-auto px-2 sm:px-3 lg:px-4">
    <div id="productTable" class="w-full" x-data="productTable()">
        <div class="flex flex-wrap mx-auto mt-2 p-4 bg-white rounded shadow-md mb-2">
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
        @include('supplier_products.partial_supplier_product_table')
        <div class="mx-auto mt-2 max-w-full">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 m-2">
                <div class="lg:col-span-3 md:col-span-1"></div>
                <nav class="lg:col-span-7 col-span-1 md:col-span-9 pagination" x-html="links"></nav>
                <div class="lg:col-span-2 col-span-1 md:col-span-2 justify-end">
                    <div class="flex items-center space-x-2">
                        <label for="perPage" class="text-sm flex-grow text-right pr-2">Số hàng:</label>
                        <select x-model="perPage" @change="fetchData(urls.baseUrl)" class="px-1 py-2 text-sm w-20">
                            <option value="15">15</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
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
            products: [],
            suppliers: @json($suppliers),
            searchParams: {
                searchProductCode: '',
                group: '',
                status: '',
            },
            perPage: 15,
            links: '',
            newSupplier: {
                supplier_id: '',
                provider: '1688',
                url: '',
                product_id: ''
            },
            init() {
                this.fetchData(urls.baseUrl);
            },
            fetchData(baseUrl) {
                const url = new URL(baseUrl);
                url.searchParams.set('perPage', this.perPage);
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
                    this.links = data.links;
                    console.log(this.products);
                });
            },
            submitForm() {
                this.fetchData(urls.baseUrl);
            },
            extractProductId() {
                const url = this.newSupplier.url;
                const id = this.getProductIdFromUrl(url);
                this.newSupplier.product_id = id ? id : 'Không hợp lệ';
            },
            getProductIdFromUrl(url) {
                const url1688Pattern = /offer\/(\d+).html/;
                const urlTaobaoTmallPattern = /id=(\d+)/;
                if (url.includes('1688.com')) {
                    const match = url.match(url1688Pattern);
                    return match ? match[1] : null;
                } else if (url.includes('taobao.com') || url.includes('tmall.com')) {
                    const match = url.match(urlTaobaoTmallPattern);
                    return match ? match[1] : null;
                }
                return null;
            },
            addSupplier(product) {
                fetch('{{ route('rapidapi.fetch') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        supplier_id: this.newSupplier.supplier_id,
                        provider: this.newSupplier.provider,
                        product_id: this.newSupplier.product_id,
                        product_api_id: product.product_api_id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    product.supplier_products.push(data); // Thêm dữ liệu nhà cung cấp mới vào danh sách
                    this.newSupplier.supplier_id = '';
                    this.newSupplier.product_id = '';
                    product.showAddSupplierForm = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            },
            editSupplierLink(link) {
                this.newSupplier = {
                    supplier_id: link.supplier_id,
                    provider: link.supplier_group_id == 1 ? '1688' : 'taobao',
                    url: link.url,
                    product_id: link.product_id,
                };
                this.extractProductId();
            },
            saveSupplierLink(link) {
                console.log(link);
                fetch(`supplier-links/${link.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(link)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // this.fetchProducts();
                        } else {
                            console.error(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            },
            removeSupplierFromProduct(link) {
                fetch(`supplier-links/${link.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.fetchProducts();
                        } else {
                            console.error(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            },
        }));
    });
</script>
@endpush
