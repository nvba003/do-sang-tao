@extends('layouts.app')

@section('content')
<div class="px-2 sm:px-3 lg:px-4">
    <div id="orderTable" class="w-full" x-data="orderTable()">
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
        <div class="grid grid-cols-1 gap-4">
            <template x-for="cart in purchaseCarts" :key="cart.id">
                <div class="bg-white shadow-md rounded-lg p-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-red-100">
                                <!-- Supplier Information -->
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <h2 class="font-bold text-lg">Nhà Cung Cấp</h2>
                                    <p>Tên: <span x-text="cart.supplier.name"></span></p>
                                </th>
                                <!-- Supplier Name and Link -->
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <h2 class="font-bold text-lg">Tên và Link Nhà Cung Cấp</h2>
                                    <p>Link: <a :href="cart.link" target="_blank" class="text-blue-500" x-text="cart.supplier_name"></a></p>
                                </th>
                                <!-- General Information -->
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <h2 class="font-bold text-lg">Thông Tin Chung</h2>
                                    <p>Ghi Chú: <span x-text="cart.notes"></span></p>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Purchase Cart Items -->
                            <template x-for="item in cart.items" :key="item.id">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p>Sản Phẩm: <span x-text="item.product.name"></span></p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p>SKU Nhà Cung Cấp: <span x-text="item.supplier_product_sku_id"></span></p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p>Số Lượng: <span x-text="item.quantity"></span></p>
                                        <p>Đơn Giá: <span x-text="item.unit_price"></span></p>
                                        <p>Ghi Chú: <span x-text="item.notes"></span></p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <!-- Notes and Logistics Information -->
                    <div class="mt-4 bg-gray-10
                    0">
                        <h2 class="font-bold text-lg">Thông Tin Ghi Chú và Nhà Vận Chuyển</h2>
                        <p>Ghi Chú: <span x-text="cart.notes"></span></p>
                        <p>Nhà Vận Chuyển: 
                            <select name="logistics_delivery_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <template x-for="delivery in logisticsDeliveries" :key="delivery.id">
                                    <option :value="delivery.id" x-text="delivery.name"></option>
                                </template>
                            </select>
                        </p>
                    </div>
                </div>
            </template>
        </div>
    </div>
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
@endsection

@push('scripts')
<script>
    const urls = {
        baseUrl: "{{ url('/purchase-carts') }}",
    };
    document.addEventListener('alpine:init', () => {
        Alpine.data('orderTable', () => ({
            purchaseCarts: [],
            suppliers: @json($suppliers),
            logisticsDeliveries: @json($logisticsDeliveries),
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
                    this.purchaseCarts = data.purchaseCarts;
                    this.links = data.links;
                    console.log(this.purchaseCarts);
                });
            },
            submitForm() {
                this.fetchData(urls.baseUrl);
            },
        }));
    });
</script>
@endpush
