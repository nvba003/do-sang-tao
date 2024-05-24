
<template x-for="(containers, productId) in products" :key="productId" >
    <tbody>
        <tr :class="{'bg-blue-100': containers.showDetails}" class="border-b">
            <td class="p-2 w-1/24 text-center mt-2 hidden sm:block">
                <input type="checkbox" class="checkItem">
            </td>
            <td class="p-2 w-1/24 whitespace-nowrap">
                <button @click="containers.showDetails = !containers.showDetails" x-text="containers.showDetails ? '-' : '+'" class="bg-blue-500 text-white p-2 rounded"></button>
            </td>
            <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="Object.values(containers)[0][0].product_api.name"></td>
            <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="totalGroupQuantity(Object.values(containers))"></td>
            <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="sumSelectedQuantities(Object.values(containers))"></td>
            <td class="w-2/24 px-6 py-4 whitespace-nowrap" x-text="Object.keys(containers).length"></td>
            <td class="w-5/24 px-6 py-4 whitespace-nowrap">
                <button @click="selectAllContainers(Object.values(containers))" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                    Lấy đủ
                </button>
            </td>
        </tr>
        <tr x-show="containers.showDetails" x-cloak class="bg-blue-50">
            <td colspan="100%">
                <div class="flex flex-wrap -mx-2">
                    <template x-for="(containerGroup, containerGroupId) in Object.entries(containers).filter(([key, value]) => key !== 'showDetails')" :key="containerGroupId">
                        <div class="flex flex-row items-left w-full p-1 m-2 border-b">
                            <div class="p-2 flex flex-col items-left justify-center w-1/4 h-28 text-sm border rounded bg-green-500 bg-opacity-75 hover:bg-opacity-100">
                                <span x-text="'Mã thùng: ' + containerGroup[1][0].container.container_code" class="text-xs font-semibold"></span>  
                                <span x-text="'Vị trí: ' + containerGroup[1][0].container.location.parent.location_name" class="text-xs"></span>  
                                <span x-text="'Thùng số: ' + containerGroup[1][0].container.location.location_name" class="text-xs"></span>
                                <span x-text="'SL trong thùng: ' + containerGroup[1][0].container.product_quantity" class="text-xs"></span> 
                                <span x-text="'ID SP: ' + containerGroup[1][0].branch_id" class="text-xs"></span>
                                <span x-text="'ID SP: ' + containerGroup[1][0].product_api_id" class="text-xs"></span>
                            </div>
                            <template x-for="container in containerGroup[1]" :key="container.id">
                                <div x-data="containerManager()" class="mx-2 rounded border flex flex-col items-left justify-center w-24 h-28">
                                    <div @click="selectContainer(container)" class="p-2 border rounded cursor-pointer hover:bg-green-500 flex flex-col items-left justify-center w-24 h-24"
                                    :class="{'bg-green-300': container.isSelected, 'bg-gray-200': !container.isSelected, 'border-orange-500': container.quantity !== container.auxpacking_product.quantity }">
                                        <span x-text="container.order.platform.name + ' :'" class="text-xs font-semibold"></span>    
                                        <span x-text="container.order.order_code" class="text-xs"></span>
                                        <div class="flex items-center space-x-2">
                                            <input x-model="container.quantity" type="number" min="1" @change="updateQuantity(container)" class="text-xs border rounded p-1 w-12" :class="{'bg-gray-400': container.isSelected}" :disabled="container.isSelected">
                                            <span x-text="'/' + container.auxpacking_product.quantity" class="text-xs"></span>
                                        </div>
                                        <span x-text="'Note: ' + container.notes || ''" class="text-xs"></span>
                                    </div> 
                                    <div class="relative border rounded-lg flex flex-col items-center justify-center h-24">
                                        <div class="flex w-full justify-between">
                                            <button @click.stop="openAddFormWithCopy(container)" class="flex-1 text-center cursor-pointer text-sm text-white bg-green-400 rounded px-1">+</button>
                                            <button @click.stop="removeContainer(container)" class="flex-1 text-center cursor-pointer text-sm text-white bg-red-400 rounded px-1">x</button>
                                        </div>
                                        <div x-show="showAddContainerForm" class="fixed inset-0 bg-black bg-opacity-25 flex items-center justify-center z-10">
                                            <div class="bg-white p-2 border border-gray-300 shadow-lg rounded-lg max-w-md w-full">
                                                <div class="flex flex-wrap -mx-1">
                                                    <div class="w-1/2 p-1" x-data="{ orderId: '' }" x-init="orderId = container.order.order_code">
                                                        <input type="text" x-model="orderId" class="text-xs border rounded p-1 w-full bg-gray-300" disabled>
                                                    </div>
                                                    <div class="w-1/4 p-1" x-data="{ filteredContainers: [] }" x-init="filteredContainers = allContainers.filter(container => container.product_id === containerGroup[1][0].product_api_id && container.branch_id === containerGroup[1][0].branch_id)">
                                                        <select x-model="newContainer.selectedContainerId" class="text-xs border rounded p-1 w-full bg-gray-100">
                                                            <template x-for="container in filteredContainers" :key="container.id">
                                                                <option x-bind:value="container.id" x-text="container.container_code"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                    <!-- <div class="w-1/4 p-1">
                                                        <input type="text" x-data="{ initValue: '' }" x-init="initValue = containerGroup[1][0].container.container_code" x-model="initValue" class="text-xs border rounded p-1 w-full bg-gray-100">
                                                    </div> -->
                                                    <div class="w-1/4 p-1">
                                                        <input x-model="newContainer.quantity" type="number" placeholder="Số lượng" class="text-xs border rounded p-1 w-full">
                                                    </div>
                                                    <div class="w-1/2 p-1" x-data="{ productId: '' }" x-init="productId = container.product_api.product_api_id">
                                                        <input type="text" x-model="productId" class="text-xs border rounded p-1 w-full bg-gray-300" disabled>
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
    </tbody>
</template>


