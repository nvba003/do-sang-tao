@extends('layouts.app')

@section('content')
<div class="container mx-auto px-2 sm:px-3 lg:px-4" x-data="shoppingListPage()">
    <h1 class="text-2xl font-bold mb-4">Danh sách đặt hàng</h1>
    <div class="flex justify-between mb-4">
        <button @click="calculateOrderList" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Tính toán đặt hàng</button>
    </div>
    <div class="bg-white shadow-md rounded-lg my-1 overflow-x-auto p-2 sm:p-4">
        <table class="w-full bg-white border border-gray-200 rounded-lg">
            <thead class="text-white bg-gray-500">
                <tr>
                    <th class="w-1/24 px-2 py-2 text-left text-center text-xs md:text-sm font-semibold uppercase tracking-wider"></th>
                    <th class="w-3/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">SKU</th>
                    <th class="w-9/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Tên sản phẩm</th>
                    <th class="w-3/24 px-2 py-2 text-center text-xs md:text-sm font-semibold uppercase tracking-wider">Nhóm</th>
                    <th class="w-2/24 px-2 py-2 text-right text-xs md:text-sm font-semibold uppercase tracking-wider">Tình trạng</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="shoppingList in shoppingLists" :key="shoppingList.id">
                    <tr :class="{'bg-blue-200': shoppingList.showDetails, 'bg-white': !shoppingList.showDetails}" class="border-b">
                        <td class="p-2 w-1/24 whitespace-nowrap">
                            <button @click="toggleDetails(shoppingList)" x-text="shoppingList.showDetails ? '-' : '+'" class="bg-blue-500 w-6 h-6 text-white rounded"></button>
                        </td>
                        <td class="w-3/24 px-2 py-3 whitespace-nowrap" x-text="shoppingList.sku"></td>
                        <td class="w-9/24 px-2 py-3 whitespace-normal break-words" x-text="shoppingList.name"></td>
                        <td class="w-3/24 px-2 py-3 whitespace-nowrap" x-text="getGroup(shoppingList.product_group_id)"></td>
                        <td class="w-2/24 px-2 py-3 whitespace-nowrap text-right" x-text="shoppingList.out_of_stock ? 'Hết hàng' : 'Còn hàng'"></td>
                    </tr>
                    <template x-if="shoppingList.showDetails">
                        <tr class="bg-blue-100 border-b">
                            <td colspan="100%" class="text-xs md:text-sm">
                                <div class="flex flex-col w-full p-2 rounded-lg">
                                    <table class="w-full bg-gray-100 border border-gray-200 rounded-lg">
                                        <thead class="bg-blue-500 text-white text-sm md:text-base rounded-lg">
                                            <tr>
                                                <th class="py-2 px-2 text-left font-normal">Tên Nhà cung cấp</th>
                                                <th class="py-2 px-2 text-left font-normal">Đánh giá</th>
                                                <th class="py-2 px-1 text-left font-normal">ID Sản phẩm</th>
                                                <th class="py-2 px-1 text-left font-normal">URL Sản phẩm</th>
                                                <th class="py-2 px-1 text-center font-normal">Có sẵn</th>
                                                <th class="py-2 px-1 text-center font-normal">Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="supplier in shoppingList.suppliers" :key="supplier.id">
                                                <tr class="border-t border-gray-200">
                                                    <td class="p-1 w-3/24" x-text="supplier.name"></td>
                                                    <td class="p-1 w-10/24" x-text="supplier.reviews_avg_rating"></td>
                                                    <td class="p-1 w-10/24" x-text="supplier.pivot.supplier_product_id"></td>
                                                    <td class="p-1 w-3/24"><a :href="supplier.pivot.supplier_product_url" target="_blank" class="text-blue-500">Link</a></td>
                                                    <td class="p-1 w-1/24 text-center" x-text="supplier.pivot.available ? 'Yes' : 'No'"></td>
                                                    <td class="p-1 w-1/24 items-center">
                                                        <button @click="orderFromSupplier(supplier)" class="bg-green-500 text-white text-xs md:text-sm font-bold py-2 px-4 rounded">Đặt hàng</button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </template>
                </template>
            </tbody>
        </table>
    </div>
</div>

<script>
    function shoppingListPage() {
        return {
            shoppingLists: @json($shoppingLists),
            selectedItems: [],
            selectedCount: 0,
            checkAll: false,
            searchParams: {
                searchProductCode: '',
                group: '',
                status: '',
            },
            updateCount() {
                this.selectedCount = this.selectedItems.length;
            },
            toggleAll() {
                this.checkAll = !this.checkAll;
                this.selectedItems = this.checkAll ? this.shoppingLists.map(shoppingList => shoppingList.id) : [];
                this.updateCount();
            },
            toggleDetails(shoppingList) {
                shoppingList.showDetails = !shoppingList.showDetails;
            },
            orderFromSupplier(supplier) {
                // Logic để đặt hàng từ nhà cung cấp
                console.log('Đặt hàng từ nhà cung cấp:', supplier);
            },
            getGroup(id) {
                // Logic để lấy nhóm
            },
            calculateOrderList() {
                // Logic để tính toán danh sách đặt hàng
                console.log('Tính toán danh sách đặt hàng');
            },
            formatAmount(amount) {
                return new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 0 }).format(amount);
            },
        };
    }
</script>
@endsection
