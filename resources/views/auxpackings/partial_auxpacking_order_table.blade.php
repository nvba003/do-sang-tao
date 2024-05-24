<template x-for="order in orders" :key="order.id">
    <tbody>
        <!-- Row for order details -->
        <tr :class="{'bg-blue-100': order.showDetails, 'bg-white': !order.showDetails}" class="border-b">
            <td class="p-2 w-1/24 text-center mt-2 hidden sm:block">
                <input type="checkbox" class="checkItem">
            </td>
            <td class="p-2 w-1/24 whitespace-nowrap">
                <button @click="order.showDetails = !order.showDetails" x-text="order.showDetails ? '-' : '+'" class="bg-blue-500 p-2 text-white rounded"></button>
            </td>
            <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="order.order.order_code"></td>
            <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="order.order.customer_account.account_name"></td>
            <td class="w-2/24 px-6 py-4 whitespace-nowrap" x-text="order.order.total_amount"></td>
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
                                <div class="p-2 flex flex-col items-left justify-center w-1/3 h-24 text-sm border rounded bg-green-500 bg-opacity-75 hover:bg-opacity-100">
                                    <span x-text="'Mã SP: ' + product.product_api.name" class="font-semibold"></span>
                                    <input x-model="product.quantity" class="text-xs p-1 border rounded w-16 ml-2" type="number" min="1" @change="updateQuantity(product)">
                                    <span x-text="'Status: ' + (product.status ? 'Đã lấy' : 'Chưa lấy')"></span>
                                    <span x-text="'Ghi chú: ' + product.notes"></span>
                                </div>
                                <template x-for="container in product.containers" :key="container.id">
                                    <div @click="selectContainer(container)" :class="{'bg-green-300': container.isSelected, 'bg-gray-200': !container.isSelected}" class="p-2 mx-2 border rounded-lg cursor-pointer hover:bg-green-500 flex flex-col items-left justify-center w-24 h-24">
                                        <span x-text="'ID: ' + container.container.container_code" class="text-xs font-semibold"></span>
                                        <span x-text="'Vị trí: ' + container.container.location.parent.location_name" class="text-xs"></span>
                                        <span x-text="'Số thùng: ' + container.container.location.location_name" class="text-xs"></span>
                                        <input x-model="container.quantity" class="text-xs p-1 border rounded w-16" type="number" min="1" @change="updateQuantity(container)">
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
