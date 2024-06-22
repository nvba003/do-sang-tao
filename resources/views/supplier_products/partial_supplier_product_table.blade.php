<div class="bg-white shadow-md rounded-lg my-1 overflow-x-auto p-2 sm:p-4">
    <div>Đã chọn: <span class="bg-blue-500 text-white px-2 py-0 rounded-lg" x-text="selectedCount"></span> hàng</div>
    <table class="w-full bg-white border border-gray-200 rounded-lg">
        <thead class="text-white bg-gray-500">
            <tr>
                <th scope="col" class="w-1/24 px-2 py-2 mt-1 text-center text-xs md:text-sm hidden sm:block font-semibold uppercase tracking-wider">
                    <input type="checkbox" x-model="checkAll" @click="toggleAll">
                </th>
                <th scope="col" class="w-1/24 px-2 py-2 text-left text-center text-xs md:text-sm font-semibold uppercase tracking-wider"></th>
                <th scope="col" class="w-3/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">SKU</th>
                <th scope="col" class="w-9/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Tên sản phẩm</th>
                <th scope="col" class="w-3/24 px-2 py-2 text-center text-xs md:text-sm font-semibold uppercase tracking-wider">Danh mục</th>
                <th scope="col" class="w-3/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Nhóm</th>
                <th scope="col" class="w-1/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Combo</th>
                <th scope="col" class="w-1/24 px-2 py-2 text-right text-xs md:text-sm font-semibold uppercase tracking-wider">SL</th>
                <th scope="col" class="w-2/24 px-2 py-2 text-center text-xs md:text-sm font-semibold uppercase tracking-wider text-right">Giá lẻ</th>
            </tr>
        </thead>
        
        <template x-for="product in products" :key="product.id">
            <tbody>
                <tr :class="{'bg-blue-200': product.showDetails, 'bg-white': !product.showDetails}" class="border-b">
                    <td class="p-2 w-1/24 text-center mt-2 hidden sm:block">
                        <input type="checkbox" class="checkItem" :value="product.id" x-model="selectedItems" @change="updateCount">
                    </td>
                    <td class="p-2 w-1/24 whitespace-nowrap">
                        <button @click="product.showDetails = !product.showDetails" x-text="product.showDetails ? '-' : '+'" class="bg-blue-500 w-6 h-6 text-white rounded"></button>
                    </td>
                    <td class="w-3/24 px-2 py-3 whitespace-nowrap" x-text="product.sku"></td>
                    <td class="w-9/24 px-2 py-3 whitespace-normal break-words" x-text="product.name"></td>
                    <td class="w-3/24 px-2 py-3 whitespace-nowrap" x-text="getCategory(product.category_id)"></td>
                    <td class="w-3/24 px-2 py-3 whitespace-nowrap" x-text="getGroup(product.product_group_id)"></td>
                    <td class="w-1/24 px-2 py-3 whitespace-nowrap text-right mr-2" x-text="product.bundle_id ? '✔️' : '_'"></td>
                    <td class="w-1/24 px-2 py-3 whitespace-nowrap text-right" x-text="formatAmount(product.quantity)"></td>
                    <td class="w-2/24 px-2 py-3 whitespace-nowrap text-right" x-text="formatAmount(product.price)"></td>
                </tr>
                <template x-if="product.showDetails">
                    <tr class="bg-blue-100 border-b">
                        <td colspan="100%" class="text-xs md:text-sm">
                            <div class="flex flex-col w-full p-2 rounded-lg">
                                <div class="mb-2">
                                    <button @click="addSupplierToProduct(product)" class="bg-green-500 text-white px-4 py-2 rounded mb-4">Thêm nhà cung cấp</button>
                                </div>
                                <table class="w-full bg-gray-100 border border-gray-200 rounded-lg">
                                    <thead class="bg-blue-500 text-white text-sm md:text-base rounded-lg">
                                        <tr>
                                            <th class="py-2 px-2 text-left font-normal">Tên Nhà cung cấp</th>
                                            <th class="py-2 px-2 text-left font-normal">ID Sản phẩm</th>
                                            <th class="py-2 px-1 text-left font-normal">URL Sản phẩm</th>
                                            <th class="py-2 px-1 text-center font-normal">Có sẵn</th>
                                            <th class="py-2 px-1 text-center font-normal">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="supplier in product.suppliers" :key="supplier.id">
                                            <tr class="border-t border-gray-200">
                                                <td class="p-1 w-3/24" x-text="supplier.name"></td>
                                                <td class="p-1 w-10/24" x-text="supplier.pivot.supplier_product_id"></td>
                                                <td class="p-1 w-3/24"><a :href="supplier.pivot.supplier_product_url" target="_blank" class="text-blue-500">Link</a></td>
                                                <td class="p-1 w-1/24 text-center" x-text="supplier.pivot.available ? 'Yes' : 'No'"></td>
                                                <td class="p-1 w-1/24 items-center">
                                                    <button @click="removeSupplierFromProduct(product, supplier)" class="bg-red-500 text-white text-xs md:text-sm font-bold py-2 px-4 rounded">Xóa</button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </template>
    </table>
</div>