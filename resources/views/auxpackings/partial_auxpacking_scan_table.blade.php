<div class="bg-white shadow-md rounded-lg my-1 overflow-x-auto p-2 sm:p-4">
    <div class="flex items-center px-4 py-1 bg-white">
        <div>Đã chọn: <span class="bg-blue-500 text-white px-2 py-1 rounded-lg" x-text="selectedCount"></span> hàng</div>
        <button @click="deliveryHandoff" class="bg-green-500 text-white px-4 py-2 ml-4 rounded">Giao bưu tá</button>
    </div>
    <table class="w-full bg-white border border-gray-200 rounded-lg">
        <thead class="text-white bg-gray-500">
            <tr>
                <th scope="col" class="w-1/24 px-2 py-2 mt-1 text-center text-xs md:text-sm hidden md:block font-semibold uppercase tracking-wider">
                    <input type="checkbox" x-model="checkAll" @click="toggleAll">
                </th>
                <th scope="col" class="w-5/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Thời gian</th>
                <th scope="col" class="w-4/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Mã vận chuyển</th>
                <th scope="col" class="w-4/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Mã đơn</th>
                <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Kênh</th>
                <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Người quét</th>
                <th scope="col" class="w-2/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>

        <template x-for="order in orders" :key="order.id">
            <tr :class="scanClass(order)" class="border-b">
                <td class="p-2 w-1/24 text-center mt-2 hidden sm:block">
                    <input type="checkbox" class="checkItem" :value="order.id" x-model="selectedItems" @change="updateCount">
                </td>
                <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="formatDate(order.created_at)"></td>
                <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="order.tracking_number"></td>
                <td class="w-5/24 px-6 py-4 whitespace-nowrap" x-text="order.order.order_code"></td>
                <td class="w-3/24 px-6 py-4 whitespace-nowrap" x-text="order.platform.name"></td>
                <td class="w-3/24 px-6 py-4 whitespace-nowrap" x-text="order.user.name"></td>
                <td class="w-2/24 px-6 py-4 whitespace-nowrap">
                    <button @click="deleteScan(order.id)" class="bg-red-500 hover:bg-red-700 text-white py-2 px-4 rounded">
                        Xóa
                    </button>
                </td>
            </tr>
        </template>

    </table>
</div>