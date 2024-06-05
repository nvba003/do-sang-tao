<div class="bg-white shadow-md rounded-lg my-1 overflow-x-auto p-2 sm:p-4">
    <div>Đã chọn: <span class="bg-blue-500 text-white px-2 py-0 rounded-lg" x-text="selectedCount"></span> hàng</div>
    <!-- <button @click="updateOrders" class="bg-green-500 text-white px-4 py-2 rounded mb-4">Update orders</button> -->
        <table class="w-full bg-white border border-gray-200 rounded-lg">
            <thead class="text-white bg-gray-500">
                <tr>
                    <th scope="col" class="w-1/24 px-2 py-2 mt-1 text-center text-xs md:text-sm hidden md:block font-semibold uppercase tracking-wider">
                        <input type="checkbox" x-model="checkAll" @click="toggleAll">
                    </th>
                    <th scope="col" class="w-1/24 px-2 py-3 text-left text-center text-xs md:text-sm font-semibold uppercase tracking-wider"></th>
                    <th scope="col" class="w-2/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">ID</th>
                    <th scope="col" class="w-1/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">SL</th>
                    <th scope="col" class="w-1/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Đã lấy</th>
                    <th scope="col" class="w-1/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Còn</th>
                    <th scope="col" class="w-1/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Vị trí</th>
                    <th scope="col" class="w-1/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">No.</th>
                    <th scope="col" class="hidden md:table-cell w-12/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Tên sản phẩm</th>
                    <th scope="col" class="hidden md:table-cell w-2/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Số đơn</th>
                    <th scope="col" class="w-1/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>

<template x-for="(group, containerId) in containers" :key="containerId">
    <tbody> 
        <tr :class="{
                        'bg-blue-100': group[0].showDetails,
                        'bg-white': !group[0].showDetails && sumSelectedQuantities(group) === 0,
                        'bg-yellow-200': sumSelectedQuantities(group) > 0 && sumSelectedQuantities(group) < totalGroupQuantity(group),
                        'bg-green-200': sumSelectedQuantities(group) === totalGroupQuantity(group),
                        'bg-red-200': sumSelectedQuantities(group) > totalGroupQuantity(group)
                    }" class="border-b">
            <td class="p-2 w-1/24 text-center mt-2 hidden md:block">
                <input type="checkbox" class="checkItem" :value="containerId" x-model="selectedItems" @change="updateCount">
            </td>
            <td class="p-2 w-1/24 whitespace-nowrap">
                <button @click="group[0].showDetails = !group[0].showDetails" x-text="group[0].showDetails ? '-' : '+'" class="bg-blue-500 text-white w-6 h-6 rounded"></button>
            </td>
            <td class="w-2/24 px-6 py-4 whitespace-nowrap" x-text="group[0].container.container_code"></td>
            <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="totalGroupQuantity(group)"></td>
            <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="sumSelectedQuantities(group)"></td>
            <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="remaining(group)"></td>
            <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="group[0].container.location.parent.location_name"></td>
            <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="group[0].container.location.location_name"></td>
            <td class="hidden md:table-cell w-12/24 px-6 py-4 whitespace-nowrap" x-text="group[0].product_api.name"></td>
            <td class="hidden md:table-cell w-2/24 px-6 py-4 whitespace-nowrap" x-text="group.length"></td>
            <td class="w-1/24 px-2 py-2 whitespace-nowrap">
                <button @click="selectAllContainers(group)" x-show="allQuantitiesAboveZero(group)" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                    Lấy đủ
                </button>
            </td>
        </tr>
        <tr x-show="group[0].showDetails" x-cloak class="bg-blue-50">
            <td colspan="100%">
                <div class="flex flex-row items-left w-full p-1 m-2 border-b">
                    <template x-for="container in group" :key="container.id">
                        <div @click="selectContainer(container)" :class="containerClass(container)" class="p-2 mx-2 border rounded-lg cursor-pointer hover:bg-green-500 flex flex-col items-left justify-center w-30 h-24">
                            <span x-text="container.order.platform.name + ' :'" class="text-xs font-semibold"></span>    
                            <span x-text="container.order.order_code" class="text-xs"></span>
                            <span x-text="'SKU: ' + container.product_api.sku" class="text-xs"></span>
                            <div class="flex items-center space-x-2">
                                <input x-model="container.quantity" type="number" min="1" class="text-xs border rounded p-1 w-12" 
                                :class="{'bg-gray-400': container.isSelected}" :disabled="container.isSelected" @click.stop>
                                <span x-text="'/' + container.container.product_quantity" class="text-xs"></span>
                            </div>
                            <span x-text="container.notes || ''" class="text-xs"></span>
                        </div>
                    </template>
                </div>
            </td>
        </tr>
    </tbody>
</template>

</table>
</div>

