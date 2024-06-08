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
                <td class="w-3/24 px-2 py-3 whitespace-nowrap" x-text="getCategory(product.category_id)"></td>
                <td class="w-3/24 px-2 py-3 whitespace-nowrap" x-text="getGroup(product.product_group_id)"></td>
                <td class="w-1/24 px-2 py-3 whitespace-nowrap text-right mr-2" x-text="product.bundle_id ? '✔️' : '_'"></td>
                <td class="w-2/24 px-2 py-3 whitespace-nowrap text-right" x-text="formatAmount(product.price)"></td>
            </tr>
            <template x-if="product.showDetails">
                <tr class="bg-blue-100 border-b">
                    <td colspan="100%" class="text-xs md:text-sm">
                        <div class="flex flex-col w-full p-2 rounded-lg">
                            <table class="w-full bg-gray-100 border border-gray-200 rounded-lg">
                                <thead class="bg-blue-500 text-white text-sm md:text-base rounded-lg">
                                    <tr>
                                        <th class="py-2 px-2 text-left font-normal">SKU</th>    
                                        <th class="py-2 px-2 text-left font-normal">Tên sản phẩm</th>
                                        <th class="py-2 px-1 text-left font-normal">Danh mục</th>
                                        <th class="py-2 px-1 text-center font-normal">Nhóm</th>
                                        <th class="py-2 px-1 text-center font-normal">Combo</th>
                                        <th class="py-2 px-1 text-center font-normal">Lưu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-t border-gray-200">
                                        <td class="p-1 w-3/24">
                                            <input x-model="product.sku" class="text-xs md:text-sm p-2 border rounded" type="text">
                                        </td>
                                        <td class="p-1 w-10/24">
                                            <textarea x-model="product.name" class="text-xs md:text-sm w-full p-1 border rounded resize-y"></textarea>
                                        </td>
                                        <td class="p-1 w-3/24">
                                            <select class="bg-white text-xs md:text-sm rounded p-2 w-full" x-model="product.category_id">
                                                <option value="">Chọn</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-1 w-3/24">
                                            <select class="bg-white text-xs md:text-sm rounded p-2 w-full" x-model="product.product_group_id">
                                                <option value="">Chọn</option>
                                                @foreach($productGroups as $group)
                                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-1 w-1/24 text-center">
                                            <template x-if="product.bundle_id === null">
                                                <button @click="addBundle(product)" class="text-xs text-center md:text-sm bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded">Thêm</button>
                                            </template>
                                            <template x-if="product.bundle_id !== null && (userRole === 'admin' || userRole === 'manager')">
                                                <button @click="removeBundle(product)" class="text-xs text-center md:text-sm bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-3 rounded">Xóa</button>
                                            </template>
                                        </td>
                                        <td class="p-1 w-1/24 items-center">
                                            <button @click="updateInfo(product)" class="bg-green-500 text-white text-xs md:text-sm font-bold py-2 px-4 rounded">Lưu</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <template x-if="product.bundle_id !== null">
                                <table class="min-w-full bg-yellow-50 mt-2 rounded-lg">
                                    <thead>
                                        <tr class="rounded-t-lg bg-yellow-300 border-b-2 border-gray-200 bg-gray-100 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">
                                            <th class="w-5/12 py-2 px-2">Tên Combo</th>
                                            <th class="w-1/12 py-2 px-2">Giá</th>
                                            <th class="w-2/12 py-2 px-2">Loại</th>
                                            <th class="w-2/12 py-2 px-2">Mô tả</th>
                                            <th class="w-2/12 p-2">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="py-1 px-1 border-b border-gray-200">
                                                <input x-model="product.bundle.name" type="text" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                            </td>
                                            <td class="py-1 px-1 border-b border-gray-200">
                                                <input x-model="product.bundle.price" type="number" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                            </td>
                                            <td class="py-1 px-1 border-b border-gray-200">
                                                <select x-model="product.bundle.type" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                                    <option value="1">Combo số lượng</option>
                                                    <option value="2">Combo sản phẩm</option>
                                                </select>
                                            </td>
                                            <td class="py-1 px-1 border-b border-gray-200">
                                                <textarea x-model="product.bundle.description" class="text-xs md:text-sm w-full p-1 border rounded resize-y" rows="1"></textarea>
                                            </td>
                                            <td class="p-1 border-b border-gray-200 text-sm">
                                                <button @click.prevent="addBundleItem(product.bundle)"
                                                        x-show="!(product.bundle.type === 1 && product.bundle.bundle_items.length > 0)"
                                                        class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                                    Thêm
                                                </button>
                                                <button @click.prevent="saveBundleItems(product.bundle, product.id)" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Lưu</button>
                                            </td>
                                        </tr>
                                        <template x-if="product.bundle.bundle_items.length > 0">
                                            <tr>
                                                <td colspan="100%" class="py-1 px-4 bg-yellow-50">
                                                    <table class="min-w-full bg-yellow-100 mt-2 rounded-lg">
                                                        <thead>
                                                            <tr class="rounded-t-lg border-b-2 border-gray-200 bg-yellow-200 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">
                                                                <th class="w-9/12 py-2 px-4">Tên Sản Phẩm</th>
                                                                <th class="w-2/12 py-2 px-4">Số Lượng</th>
                                                                <th class="w-1/12 py-2 px-4 text-center">Xóa</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template x-for="(item, itemIndex) in product.bundle.bundle_items" :key="item.id">
                                                                <tr>
                                                                    <td class="py-1 px-4 border-b border-gray-200 text-sm">
                                                                        <div class="relative" x-data="autocompleteProductSetup(item)" x-init="initAutocompleteProduct">
                                                                            <input type="text" x-ref="productInput" x-model="displayProductName" 
                                                                                @blur="setTimeout(() => productSuggestions = [], 100)" class="text-sm border-gray-200 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập sản phẩm">
                                                                            <div class="absolute z-10 w-full bg-white mt-1 rounded-md shadow-lg" x-show="productSuggestions.length > 0">
                                                                                <ul>
                                                                                    <template x-for="product in productSuggestions" :key="product.id">
                                                                                        <li @click="selectProduct(product,item)" class="p-2 hover:bg-gray-100 cursor-pointer">
                                                                                            <span x-text="product.name"></span>
                                                                                        </li>
                                                                                    </template>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="py-1 px-4 border-b border-gray-200 text-sm">
                                                                        <input x-model="item.quantity" type="number" class="w-full p-1.5 text-sm border-gray-300 rounded-md shadow-sm">
                                                                    </td>
                                                                    <td class="py-1 px-4 text-center border-b border-gray-200 text-sm">
                                                                        <button @click.prevent="removeBundleItem(product.bundle.bundle_items, itemIndex)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1.5 px-2 rounded">
                                                                            Xóa
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                            <tr>
                                                                <span class="font-medium text-yellow-800 text-sm">*Lưu ý: Sau khi thay đổi phải chọn Lưu</span>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </template>
                            <table class="w-full bg-teal-300 border border-gray-200 rounded-lg mt-2">
                                <thead class="bg-teal-600 text-white text-sm md:text-base rounded-lg">
                                    <tr>
                                        <th class="py-2 px-4 text-left font-normal">Dài (cm)</th>    
                                        <th class="py-2 px-4 text-left font-normal">Rộng (cm)</th>
                                        <th class="py-2 px-4 text-left font-normal">Cao (cm)</th>
                                        <th class="py-2 px-4 text-left font-normal">Cân nặng (gram)</th>
                                        <th class="py-2 px-4 text-center font-normal">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-t border-gray-200">
                                        <td class="py-2 px-1 w-1/5">
                                            <input x-model="product.length" class="text-sm md:text-base p-2 border rounded" type="number" min="0.0" placeholder="0.0cm">
                                        </td>
                                        <td class="py-2 px-1 w-1/5">
                                            <input x-model="product.width" class="text-sm md:text-base p-2 border rounded" type="number" min="0.0" placeholder="0.0cm">
                                        </td>
                                        <td class="py-2 px-1 w-1/5">
                                            <input x-model="product.height" class="text-sm md:text-base p-2 border rounded" type="number" min="0.0" placeholder="0.0cm">
                                        </td>
                                        <td class="py-2 px-1 w-1/5">
                                            <input x-model="product.weight" class="text-sm md:text-base p-2 border rounded" type="number" min="0" placeholder="0 gram">
                                        </td>
                                        <td class="py-2 px-1 w-1/5 text-center">
                                            <button @click="updateSizeWeight(product)" class="bg-green-600 text-white text-center text-sm md:text-base font-bold py-2 px-6 rounded">Lưu</button>
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
