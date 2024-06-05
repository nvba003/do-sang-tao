<div class="bg-white shadow-md rounded-lg my-1 overflow-x-auto p-2 sm:p-4">
    <div>Đã chọn: <span class="bg-blue-500 text-white px-2 py-0 rounded-lg" x-text="selectedCount"></span> hàng</div>
    <!-- <button @click="updateProducts" class="bg-green-500 text-white px-4 py-2 rounded mb-4">Update Products</button> -->
        <table class="w-full bg-white border border-gray-200 rounded-lg">
            <thead class="text-white bg-gray-500">
                <tr>
                    <th scope="col" class="w-1/24 px-2 py-2 mt-1 text-center text-xs md:text-sm hidden sm:block font-semibold uppercase tracking-wider">
                        <input type="checkbox" x-model="checkAll" @click="toggleAll">
                    </th>
                    <th scope="col" class="w-1/24 px-2 py-3 text-left text-center text-xs md:text-sm font-semibold uppercase tracking-wider"></th>
                    <th scope="col" class="w-4/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Mã SP</th>
                    <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Tên SP</th>
                    <th scope="col" class="w-2/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Số lượng</th>
                    <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Đã lấy</th>
                    <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Còn lại</th>
                    <th scope="col" class="w-1/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>

<template x-for="(containers, productId) in products" :key="productId" >
    <tbody>
        <tr :class="getBackgroundColor(Object.values(containers), containers.showDetails)" class="border-b">
            <td class="p-2 w-1/24 text-center mt-2 hidden sm:block">
                <input type="checkbox" class="checkItem" :value="productId" x-model="selectedItems" @change="updateCount">
            </td>
            <td class="p-2 w-1/24 whitespace-nowrap">
                <button @click="containers.showDetails = !containers.showDetails" x-text="containers.showDetails ? '-' : '+'" class="bg-blue-500 text-white w-6 h-6 rounded"></button>
            </td>
            <td class="w-5/24 px-6 py-4 whitespace-nowrap" x-text="Object.values(containers)[0][0].product_api.sku"></td>
            <td class="w-12/24 px-6 py-4 whitespace-nowrap" x-text="Object.values(containers)[0][0].product_api.name"></td>
            <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="totalGroupQuantity(Object.values(containers))"></td>
            <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="sumSelectedQuantities(Object.values(containers))"></td>
            <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="remaining(Object.values(containers))"></td>
            <td class="w-2/24 px-2 py-2 whitespace-nowrap">
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
                            <div class="p-2 flex flex-col items-left justify-center w-1/4 h-28 text-sm border rounded bg-gray-300">
                                <span x-text="'Mã thùng: ' + containerGroup[1][0].container.container_code" class="text-xs font-semibold"></span>  
                                <span x-text="'Vị trí: ' + containerGroup[1][0].container.location.parent.location_name" class="text-xs"></span>  
                                <span x-text="'Thùng số: ' + containerGroup[1][0].container.location.location_name" class="text-xs"></span>
                                <span x-text="'SL trong thùng: ' + containerGroup[1][0].container.product_quantity" class="text-xs"></span> 
                                <!-- <span x-text="'CN: ' + containerGroup[1][0].branch_id" class="text-xs"></span>
                                <span x-text="'ID SP: ' + containerGroup[1][0].product_api_id" class="text-xs"></span> -->
                            </div>
                            <template x-for="container in containerGroup[1]" :key="container.id">
                                <div x-data="containerManager()" class="mx-2 rounded border flex flex-col items-left justify-center w-28 h-28">
                                    <div @click="selectContainer(container)" class="p-2 border rounded cursor-pointer hover:bg-green-500 flex flex-col items-left justify-center w-28 h-24"
                                    :class="{'bg-green-300': container.isSelected, 'bg-gray-200': !container.isSelected, 'border-orange-500': container.quantity !== container.auxpacking_product.quantity }">
                                        <span x-text="container.order.platform.name + ' :'" class="text-xs font-semibold"></span>    
                                        <span x-text="container.order.order_code" class="text-xs"></span>
                                        <div class="flex items-center space-x-2">
                                            <input x-model="container.quantity" type="number" min="1" class="text-xs border rounded p-1 w-12" 
                                            :class="{'bg-gray-400': container.isSelected}" :disabled="container.isSelected" @click.stop>
                                            <span x-text="'/' + container.auxpacking_product.quantity" class="text-xs"></span>
                                        </div>
                                        <span x-text="container.notes || ''" class="text-xs"></span>
                                    </div> 
                                    <!-- <div class="relative border rounded-lg flex flex-col items-center justify-center h-24">
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
                                    </div> -->
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
                                                    <div class="w-1/4 p-1" x-data="{ filteredContainers: [], selectedContainerId: null }"
                                                        x-init="filteredContainers = allContainers.filter(item => item.product_id === containerGroup[1][0].product_api_id && item.branch_id === containerGroup[1][0].branch_id)">
                                                        <select x-model="selectedContainerId" class="text-xs border rounded p-1 w-full bg-gray-100"
                                                                @change="newContainer.container = filteredContainers.find(c => c.id == selectedContainerId)">
                                                            <option value="">Chọn</option>
                                                            <template x-for="containerItem in filteredContainers" :key="containerItem.id">
                                                                <option :value="containerItem.id" x-text="containerItem.container_code"></option>
                                                            </template>
                                                        </select>  
                                                    </div>
                                                    <div class="w-1/4 p-1">
                                                        <input x-model="newContainer.quantity" type="number" placeholder="Số lượng" class="text-xs border rounded p-1 w-full">
                                                    </div>
                                                    <div class="w-1/2 p-1" x-data="{ productId: '', productName: '' }" 
                                                        x-init="productId = container.product_api.product_api_id; productName = container.product_api.name">
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
    </tbody>
</template>

</table>
</div>


