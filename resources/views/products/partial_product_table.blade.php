<div class="bg-white shadow-md rounded-lg my-1 overflow-x-auto p-2 sm:p-4">
    <div>Đã chọn: <span class="bg-blue-500 text-white px-2 py-0 rounded-lg" x-text="selectedCount"></span> hàng</div>
    <!-- <button @click="updateproducts" class="bg-green-500 text-white px-4 py-2 rounded mb-4">Update products</button> -->
    <table class="w-full bg-white border border-gray-200 rounded-lg">
        <thead class="text-white bg-gray-500">
            <tr>
                <th scope="col" class="w-1/24 px-2 py-2 mt-1 text-center text-xs md:text-sm hidden sm:block font-semibold uppercase tracking-wider">
                    <input type="checkbox" x-model="checkAll" @click="toggleAll">
                </th>
                <th scope="col" class="w-1/24 px-2 py-2 text-left text-center text-xs md:text-sm font-semibold uppercase tracking-wider"></th>
                <th scope="col" class="w-3/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">SKU</th>
                <th scope="col" class="w-10/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Tên sản phẩm</th>
                <th scope="col" class="w-3/24 px-2 py-2 text-center text-xs md:text-sm font-semibold uppercase tracking-wider">Danh mục</th>
                <th scope="col" class="w-3/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Nhóm</th>
                <th scope="col" class="w-1/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Combo</th>
                <th scope="col" class="w-2/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider text-right">Giá lẻ</th>
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
                <td class="w-10/24 px-2 py-3 whitespace-normal break-words" x-text="product.name"></td>
                <td class="w-3/24 px-2 py-3 whitespace-nowrap" x-text="product.category_id ? product.category.name : '_'"></td>
                <td class="w-3/24 px-2 py-3 whitespace-nowrap" x-text="product.product_group_id ? product.product_group.name : '_'"></td>
                <td class="w-1/24 px-2 py-3 whitespace-nowrap text-right mr-2" x-text="product.bundle_id ? '✔️' : '_'"></td>
                <td class="w-2/24 px-2 py-3 whitespace-nowrap text-right" x-text="formatAmount(product.price)"></td>
            </tr>
            <template x-if="product.showDetails">
                <tr class="bg-blue-100 border-b">
                    <td colspan="100%" class="text-xs md:text-base">
                        <div class="flex flex-col w-full p-2 rounded-lg">
                            <table class="w-full bg-gray-100 border border-gray-200 rounded-lg">
                                <thead class="bg-blue-500 text-white text-sm rounded-lg">
                                    <tr>
                                        <th class="py-2 px-2 text-right font-normal">SKU</th>    
                                        <th class="py-2 px-2 text-left font-normal">Tên sản phẩm</th>
                                        <th class="py-2 px-1 text-left font-normal">Danh mục</th>
                                        <th class="py-2 px-1 text-center font-normal">Nhóm</th>
                                        <th class="py-2 px-1 text-center font-normal">Combo</th>
                                        <th class="py-2 px-1 text-center font-normal">Lưu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-t border-gray-200" :class="{'bg-red-200 text-gray-500': detail.is_cancelled }">
                                        <td class="py-2 px-1 w-3/24">
                                            <input x-model="product.sku" class="text-xs p-1 border rounded" type="text">
                                        </td>
                                        <td class="py-2 px-1 w-8/24">
                                            <input x-model="product.name" class="text-xs p-1 border rounded" type="text">
                                        </td>
                                        <td class="py-2 px-1 w-1/24">
                                            <select class="bg-white text-xs rounded py-2 px-6 w-full" x-model="product.category_id">
                                                <option value="">Chọn</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="py-2 px-1 w-1/24">
                                            <select class="bg-white text-xs rounded py-2 px-6 w-full" x-model="product.product_group_id">
                                                <option value="">Chọn</option>
                                                @foreach($productGroups as $group)
                                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="py-2 px-1 w-1/24">
                                            <template x-if="product.bundle_id === null">
                                                <button @click="addBundle(product)" class="text-xs bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">Thêm</button>
                                            </template>
                                            <template x-if="product.bundle_id !== null">
                                                <div>
                                                    <div class="flex items-center">
                                                        <button @click="removeBundle(product)" class="text-xs bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded mr-2">Xóa</button>
                                                    </div>
                                                    <!-- Hiển thị chi tiết bundle -->
                                                    <table class="min-w-full bg-white mt-2">
                                                        <thead>
                                                            <tr>
                                                                <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                                                                <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Tên Bundle</th>
                                                                <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Số lượng</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template x-for="bundle in product.bundles" :key="bundle.id">
                                                                <tr>
                                                                    <td class="py-1 px-4 border-b border-gray-200 text-sm" x-text="bundle.id"></td>
                                                                    <td class="py-1 px-4 border-b border-gray-200 text-sm">
                                                                        <input x-model="bundle.name" type="text" class="w-full border-gray-300 rounded-md shadow-sm">
                                                                    </td>
                                                                    <td class="py-1 px-4 border-b border-gray-200 text-sm">
                                                                        <input x-model="bundle.quantity" type="number" class="w-full border-gray-300 rounded-md shadow-sm">
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </td>
                                        <td class="py-2 px-2 w-1/24 items-center">
                                            <button @click="" class="bg-blue-500 text-white text-xs py-0 px-2 rounded">Lưu</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="w-full bg-gray-100 border border-gray-200 rounded-lg">
                                <thead class="bg-blue-500 text-white text-sm rounded-lg">
                                    <tr>
                                        <th class="py-2 px-2 text-right font-normal">Dài</th>    
                                        <th class="py-2 px-2 text-left font-normal">Rộng</th>
                                        <th class="py-2 px-1 text-left font-normal">Cao</th>
                                        <th class="py-2 px-1 text-center font-normal">Cân nặng</th>
                                        <th class="py-2 px-1 text-center font-normal">Lưu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-t border-gray-200" :class="{'bg-red-200 text-gray-500': detail.is_cancelled }">
                                        <td class="py-2 px-1 w-1/24">
                                            <input x-model="product.length" class="text-xs p-1 border rounded" type="number" min="0" @change="updateproductTotal(product)">
                                        </td>
                                        <td class="py-2 px-1 w-1/24">
                                            <input x-model="product.width" class="text-xs p-1 border rounded" type="number" min="0" @change="updateproductTotal(product)">
                                        </td>
                                        <td class="py-2 px-1 w-1/24">
                                            <input x-model="product.height" class="text-xs p-1 border rounded" type="number" min="0" @change="updateproductTotal(product)">
                                        </td>
                                        <td class="py-2 px-1 w-1/24">
                                            <input x-model="product.weight" class="text-xs p-1 border rounded" type="number" min="0" @change="updateproductTotal(product)">
                                        </td>
                                        <td class="py-2 px-2 w-1/24 items-center">
                                            <button @click="" class="bg-blue-500 text-white text-xs py-0 px-2 rounded">Lưu</button>
                                        </td>
                                    </tr>
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
