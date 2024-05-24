<template x-for="(group, containerId) in containers" :key="containerId">
    <tbody> 
        <tr :class="{
                        'bg-blue-100': group[0].showDetails,
                        'bg-white': !group[0].showDetails && sumSelectedQuantities(group) === 0,
                        'bg-yellow-200': sumSelectedQuantities(group) > 0 && sumSelectedQuantities(group) < totalGroupQuantity(group),
                        'bg-green-200': sumSelectedQuantities(group) === totalGroupQuantity(group),
                        'bg-red-200': sumSelectedQuantities(group) > totalGroupQuantity(group)
                    }" class="border-b">
            <td class="p-2 w-1/24 text-center mt-2 hidden sm:block">
                <input type="checkbox" class="checkItem">
            </td>
            <td class="p-2 w-1/24 whitespace-nowrap">
                <button @click="group[0].showDetails = !group[0].showDetails" x-text="group[0].showDetails ? '-' : '+'" class="bg-blue-500 text-white p-2 rounded"></button>
            </td>
            <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="group[0].container.container_code"></td>
            <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="totalGroupQuantity(group)"></td>
            <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="sumSelectedQuantities(group)"></td>
            <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="group[0].container.location.parent.location_name"></td>
            <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="group[0].container.location.location_name"></td>
            <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="group[0].product_api.name"></td>
            <td class="w-2/24 px-6 py-4 whitespace-nowrap" x-text="group.length"></td>
            <td class="w-5/24 px-6 py-4 whitespace-nowrap">
                <button @click="selectAllContainers(group)" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                    Lấy đủ
                </button>
            </td>
        </tr>
        <tr x-show="group[0].showDetails" x-cloak class="bg-blue-50">
            <td colspan="100%">
                <div class="flex flex-row items-left w-full p-1 m-2 border-b">
                    <template x-for="container in group" :key="container.id">
                        <div @click="selectContainer(container)" :class="{'bg-green-300': container.isSelected, 'bg-gray-200': !container.isSelected}" class="p-2 mx-2 border rounded-lg cursor-pointer hover:bg-green-500 flex flex-col items-left justify-center w-24 h-24">
                            <span x-text="container.order.platform.name + ' :'" class="text-xs font-semibold"></span>    
                            <span x-text="container.order.order_code" class="text-xs"></span>
                            <input x-model="container.quantity" class="text-xs p-1 border rounded w-16" type="number" min="1" @change="updateQuantity(container)">
                            <span x-text="'Note: ' + container.notes || ''" class="text-xs"></span>
                        </div>
                    </template>
                </div>
            </td>
        </tr>
    </tbody>
</template>

