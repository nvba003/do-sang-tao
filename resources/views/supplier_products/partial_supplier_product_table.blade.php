<div class="bg-white shadow-md rounded-lg my-1 overflow-x-auto p-2 sm:p-4">
    <!-- <div>Đã chọn: <span class="bg-blue-500 text-white px-2 py-0 rounded-lg" x-text="selectedCount"></span> hàng</div> -->
    <table class="w-full bg-white border border-gray-200 rounded-lg">
        <thead class="text-white bg-gray-500">
            <tr>
                <!-- <th scope="col" class="w-1/24 px-2 py-2 mt-1 text-center text-xs md:text-sm hidden sm:block font-semibold uppercase tracking-wider">
                    <input type="checkbox" x-model="checkAll" @click="toggleAll">
                </th> -->
                <th scope="col" class="w-1/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider"></th>
                <th scope="col" class="w-3/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">SKU</th>
                <th scope="col" class="w-9/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Tên sản phẩm</th>
                <th scope="col" class="w-3/24 px-2 py-2 text-center text-xs md:text-sm font-semibold uppercase tracking-wider">NCC</th>
            </tr>
        </thead>
        
        <template x-for="product in products" :key="product.id">
            <tbody>
                <tr :class="{'bg-blue-200': product.showDetails, 'bg-white': !product.showDetails}" class="border-b">
                    <!-- <td class="p-2 w-1/24 text-center mt-2 hidden sm:block">
                        <input type="checkbox" class="checkItem" :value="product.id" x-model="selectedItems" @change="updateCount">
                    </td> -->
                    <td class="p-2 w-1/24 whitespace-nowrap">
                        <button @click="product.showDetails = !product.showDetails" x-text="product.showDetails ? '-' : '+'" class="bg-blue-500 w-6 h-6 text-white rounded"></button>
                    </td>
                    <td class="w-3/24 px-2 py-3 whitespace-nowrap" x-text="product.sku"></td>
                    <td class="w-9/24 px-2 py-3 whitespace-normal break-words" x-text="product.name"></td>
                    <td class="w-3/24 px-2 py-3 whitespace-nowrap flex flex-row">
                        <template x-for="link in product.supplier_links" :key="link.id">
                            <div class="rounded px-2 py-1 text-white space-x-2 mr-2" 
                                :class="{
                                    'bg-green-500': link.available == 1,
                                    'bg-orange-500': link.available == 2,
                                    'bg-red-500': link.available == 3
                                }" 
                                x-text="link.supplier ? link.supplier.name : ''">
                            </div>
                        </template>
                    </td>
                </tr>
                <tr x-show="product.showDetails" class="bg-blue-100 border-b">
                    <td colspan="100%" class="text-xs md:text-sm">
                        <div class="flex flex-col w-full p-2 rounded-lg">
                            <h1>Chi tiết sản phẩm</h1>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title" x-text="product.title"></h5>
                                    <p class="card-text"><strong>Giá: </strong><span x-text="product.price"></span></p>
                                    <p class="card-text"><strong>Nhà cung cấp: </strong><span x-text="product.nick"></span></p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <button @click="product.showAddSupplierForm = !product.showAddSupplierForm" class="bg-green-500 text-white px-4 py-2 rounded mb-4">Thêm nhà cung cấp</button>
                                <div x-show="product.showAddSupplierForm" class="mb-4">
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Chọn nhà cung cấp:</label>
                                        <select x-model="newSupplier.supplier_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            <option value="" selected disabled>Chọn nhà cung cấp</option>
                                            <template x-for="supplier in suppliers" :key="supplier.id">
                                                <option :value="supplier.id" x-text="supplier.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Provider:</label>
                                        <select x-model="newSupplier.provider" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            <option value="1688">1688</option>
                                            <option value="taobao">Taobao/Tmall</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Link sản phẩm:</label>
                                        <input x-model="newSupplier.url" @input="extractProductId" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" placeholder="Nhập link sản phẩm">
                                        <p class="mt-2 text-gray-700">Mã sản phẩm: <span x-text="newSupplier.product_id"></span></p>
                                    </div>
                                    <div class="flex justify-end">
                                        <button @click="addSupplier(product)" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Thêm</button>
                                        <button @click="product.showAddSupplierForm = false" class="bg-gray-500 text-white px-4 py-2 rounded">Hủy</button>
                                    </div>
                                </div>
                            </div>
                            <table class="w-full bg-gray-100 border border-gray-200 rounded-lg">
                                <thead class="bg-blue-500 text-white text-sm md:text-base rounded-lg">
                                    <tr>
                                        <th class="py-2 px-2 text-left font-normal">NCC</th>
                                        <th class="py-2 px-2 text-left font-normal">Giá</th>
                                        <th class="py-2 px-1 text-left font-normal">URL Sản phẩm</th>
                                        <th class="py-2 px-1 text-center font-normal">Nhóm</th>
                                        <th class="py-2 px-1 text-center font-normal">Có sẵn</th>
                                        <th class="py-2 px-1 text-center font-normal">SKU</th>
                                        <th class="py-2 px-1 text-center font-normal">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="link in product.supplier_links" :key="link.id">
                                        <tr class="border-t border-gray-200">
                                            <td class="p-1 w-3/24" x-text="link.supplier ? link.supplier.name : ''"></td>
                                            <td class="p-1 w-2/24" x-text="link.supplier_product_sku ? link.supplier_product_sku.price : '_'"></td>
                                            <td class="p-1 w-3/24"><a :href="link.url" target="_blank" class="text-blue-500">Link</a></td>
                                            <td class="p-1 w-1/24 text-center" x-text="link.supplier ? link.supplier.group.name : ''"></td>
                                            <td class="p-1 w-2/24 text-center">
                                                <select x-model="link.available" class="shadow appearance-none border rounded w-full py-2 px-1 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    <option value="1">Còn hàng</option>
                                                    <option value="2">Hết hàng</option>
                                                    <option value="3">NCC xóa</option>
                                                </select>
                                            </td>
                                            <td class="p-1 w-3/24">
                                                <select x-model="link.supplier_product_sku_id" class="text-xs md:text-sm p-2 border rounded max-w-80" :disabled="!link.supplier_product">
                                                    <option value="">Chọn SKU</option>
                                                    <template x-if="link.supplier_product">
                                                        <template x-for="sku in link.supplier_product.supplier_skus" :key="sku.id">
                                                            <option :value="sku.id" :selected="sku.id === link.supplier_product_sku_id" x-text="sku.prop_value"></option>
                                                        </template>
                                                    </template>
                                                </select>
                                            </td>
                                            <td class="p-1 w-6/24 items-center">
                                                <button @click="product.showAddSupplierForm = true; editSupplierLink(link)" class="bg-yellow-500 text-white text-xs md:text-sm font-bold py-2 px-4 rounded">Sửa</button>
                                                <button @click="saveSupplierLink(link)" class="bg-green-500 text-white text-xs md:text-sm font-bold py-2 px-4 rounded">Lưu</button>
                                                <button @click="removeSupplierFromProduct(link)" class="bg-red-500 text-white text-xs md:text-sm font-bold py-2 px-4 rounded">Xóa</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </template>
    </table>
</div>