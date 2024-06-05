<div class="bg-white shadow-md rounded-lg my-1 overflow-x-auto p-2 sm:p-4">
    <div>Đã chọn: <span class="bg-blue-500 text-white px-2 py-0 rounded-lg" x-text="selectedCount"></span> hàng</div>
    <table class="w-full bg-white border border-gray-200 rounded-lg">
        <thead class="text-white bg-gray-500">
            <tr>
                <th scope="col" class="w-1/24 px-2 py-2 mt-1 text-center text-xs md:text-sm hidden sm:block font-semibold uppercase tracking-wider">
                    <input type="checkbox" x-model="checkAll" @click="toggleAll">
                </th>
                <th scope="col" class="w-1/24 px-2 py-3 text-left text-center text-xs md:text-sm font-semibold uppercase tracking-wider"></th>
                <th scope="col" class="w-4/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Mã đơn</th>
                <th scope="col" class="w-4/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Khách hàng</th>
                <th scope="col" class="w-2/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Tổng tiền</th>
                <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Trạng thái</th>
                <th scope="col" class="w-5/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Ghi chú</th>
                <th scope="col" class="w-4/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Kênh</th>
            </tr>
        </thead>

<template x-for="order in orders" :key="order.id">
    <tbody>
        <!-- Row for order details -->
        <tr :class="{'bg-blue-100': order.showDetails, 'bg-white': !order.showDetails}" class="border-b">
            <td class="p-2 w-1/24 text-center mt-2 hidden sm:block">
                <input type="checkbox" class="checkItem" :value="order.id" x-model="selectedItems" @change="updateCount">
            </td>
            <td class="p-2 w-1/24 whitespace-nowrap">
                <button @click="order.showDetails = !order.showDetails" x-text="order.showDetails ? '-' : '+'" class="bg-blue-500 w-6 h-6 text-white rounded"></button>
            </td>
            <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="order.order.order_code"></td>
            <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="order.order.customer_account.account_name"></td>
            <td class="w-2/24 px-6 py-4 whitespace-nowrap" x-text="formatAmount(order.order.total_amount)"></td>
            <td class="w-3/24 px-6 py-4 whitespace-nowrap" x-text="getStatus(order.status)"></td>
            <td class="w-5/24 px-6 py-4 whitespace-nowrap" x-text="order.notes || ''"></td>
            <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="order.order.platform.name"></td>
        </tr>
        <!-- Conditional row for product details -->
        <template x-if="order.showDetails">
            <tr class="bg-blue-50">
                <td colspan="100%">
                    <div class="flex flex-wrap -mx-2">
                        <template x-for="product in order.products" :key="product.id">
                            <!-- Product display -->
                            <div class="flex flex-row items-left w-full p-1 m-2 border-b">
                                <div class="p-2 flex flex-col items-left justify-center w-1/3 h-28 text-sm border rounded" :class="getBackgroundColor(product.status)">
                                    <span x-text="'SKU: ' + product.product_api.sku"></span>    
                                    <span x-text="product.product_api.name" class="font-semibold"></span>
                                    <span class="flex items-center">
                                        <span>Số lượng:</span>
                                        <span x-text="Number(product.quantity) === parseInt(product.quantity) ? parseInt(product.quantity) : parseFloat(product.quantity).toFixed(2)" class="ml-2 bg-white pl-1 pr-2 border rounded border-gray-600 shadow-sm min-w-8 text-right"></span>
                                    </span>
                                    <span x-text="'Trạng thái: ' + getProductStatus(product.status)"></span>
                                    <span x-text="product.notes || ''"></span>
                                </div>
                                <template x-for="container in product.containers" :key="container.id">
                                <div x-data="containerManager()" class="mx-2 rounded border flex flex-col items-left justify-center w-24 h-28">
                                    <div @click="selectContainer(container)" :class="{'bg-green-300': container.isSelected, 'bg-gray-200': !container.isSelected}" class="p-2 border rounded-lg cursor-pointer hover:bg-green-500 flex flex-col items-left justify-center w-24 h-24">
                                        <span x-text="'ID: ' + container.container.container_code" class="text-xs font-semibold"></span>
                                        <span x-text="'Vị trí: ' + container.container.location.parent.location_name" class="text-xs"></span>
                                        <span x-text="'Số thùng: ' + container.container.location.location_name" class="text-xs"></span>
                                        <span x-text="'Tồn: ' + container.container.product_quantity" class="text-xs"></span>
                                        <div class="flex items-center space-x-2">
                                            <input x-model="container.quantity" type="number" min="1" class="text-xs border rounded p-1 w-12" 
                                            :class="{'bg-gray-400': container.isSelected}" :disabled="container.isSelected" @click.stop>
                                            <span x-text="'/' + product.quantity" class="text-xs"></span>
                                        </div>
                                    </div>
                                    <div class="relative border rounded-lg flex flex-col items-center justify-center h-24">
                                        <div class="flex w-full justify-between">
                                            <button @click.stop="openAddFormWithCopy(container)" class="flex-1 text-center cursor-pointer text-sm text-white bg-green-400 rounded px-1">+</button>
                                            <button @click.stop="removeContainer(container)" class="flex-1 text-center cursor-pointer text-sm text-white bg-red-400 rounded px-1">x</button>
                                        </div>
                                        <div x-show="showAddContainerForm" class="fixed inset-0 bg-black bg-opacity-25 flex items-center justify-center z-10">
                                            <div class="bg-white p-2 border border-gray-300 shadow-lg rounded-lg max-w-md w-full">
                                                <div class="flex flex-wrap -mx-1">
                                                    <div class="w-1/2 p-1" x-data="{ orderId: '' }" x-init="orderId = order.order.order_code">
                                                        <input type="text" x-model="orderId" class="text-xs border rounded p-1 w-full bg-gray-300" disabled>
                                                    </div>
                                                    <div class="w-1/4 p-1" x-data="{ filteredContainers: [], selectedContainerId: null }"
                                                        x-init="filteredContainers = allContainers.filter(item => item.product_id === container.product_api_id && item.branch_id === container.branch_id)">
                                                        <select x-model="selectedContainerId" class="text-xs border rounded p-1 w-full bg-gray-100"
                                                                @change="newContainer.container = filteredContainers.find(c => c.id == selectedContainerId)">
                                                            <option value="">Chọn</option>
                                                            <template x-for="containerItem in filteredContainers" :key="containerItem.id">
                                                                <option :value="containerItem.id" x-text="containerItem.container_code"></option>
                                                            </template>
                                                        </select>  
                                                    </div>
                                                    <!-- <div class="w-1/4 p-1">
                                                        <input type="text" x-data="{ initValue: '' }" x-init="initValue = containerGroup[1][0].container.container_code" x-model="initValue" class="text-xs border rounded p-1 w-full bg-gray-100">
                                                    </div> -->
                                                    <div class="w-1/4 p-1">
                                                        <input x-model="newContainer.quantity" type="number" placeholder="Số lượng" class="text-xs border rounded p-1 w-full">
                                                    </div>
                                                    <div class="w-1/2 p-1" x-data="{ productId: '', productName: '' }" 
                                                        x-init="productId = product.product_api.product_api_id; productName = product.product_api.name">
                                                        <input type="text" x-model="productName" class="text-xs border rounded p-1 w-full bg-gray-300" disabled>
                                                    </div>
                                                    <div class="w-1/2 p-1">
                                                        <textarea x-model="newContainer.notes" placeholder="Ghi chú" class="text-xs border rounded p-1 w-full" rows="1"></textarea>
                                                    </div>
                                                </div>
                                                <div class="flex justify-right p-1">
                                                    <button @click="addNewContainer()" class="text-xs bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-4 rounded">
                                                        Lưu
                                                    </button>
                                                    <button @click="showAddContainerForm = false" class="text-xs bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-4 ml-2 rounded">
                                                        Hủy
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </td>
            </tr>
        </template>
    </tbody>
</template>

</table>
</div>
