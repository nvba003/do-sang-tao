<div id="orderTable" class="w-full" x-data="orderTable()">
    <button @click="updateOrders" class="bg-green-500 text-white px-4 py-2 rounded mb-4">Update orders</button>
    <table class="w-full bg-white border border-gray-200 rounded-lg">
        <thead class="text-white bg-gray-500">
            <tr>
                <th scope="col" class="w-1/24 px-2 py-3 mt-1 text-center text-xs md:text-sm hidden sm:block font-semibold uppercase tracking-wider">
                    <input type="checkbox" id="checkAll">
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
            <tr :class="{'bg-blue-200': order.showDetails, 'bg-white': !order.showDetails}" class="border-b">
                <td class="p-2 w-1/24 text-center mt-2 hidden sm:block">
                    <input type="checkbox" class="checkItem">
                </td>
                <td class="p-2 w-1/24 whitespace-nowrap">
                    <button @click="order.showDetails = !order.showDetails" x-text="order.showDetails ? '-' : '+'" class="bg-blue-500 p-2 text-white rounded"></button>
                </td>
                <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="order.order_code"></td>
                <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="order.customer_account.account_name"></td>
                <td class="w-2/24 px-6 py-4 whitespace-nowrap" x-text="order.total_amount"></td>
                <td class="w-3/24 px-6 py-4 whitespace-nowrap" x-text="getStatus(order.status_id)"></td>
                <td class="w-5/24 px-6 py-4 whitespace-nowrap" x-text="order.notes || ''"></td>
                <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="order.platform.name"></td>
            </tr>
            <template x-if="order.showDetails">
                <tr class="bg-blue-100">
                    <td colspan="100%" class="px-2 sm:px-4 py-2 text-xs md:text-base">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="flex items-center space-x-2">
                                <label :for="'carrier_' + order.id" class="text-gray-700">NVC:</label>
                                <select :id="'carrier_' + order.id" class="bg-white text-xs rounded py-2 px-6">
                                    <option value="">Chọn</option>
                                    @foreach($carriers as $carrier)
                                        <option value="{{ $carrier->id }}">
                                            {{ $carrier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center space-x-2">
                                <label :for="'tracking_' + order.id" class="text-gray-700">Vận đơn:</label>
                                <input type="text" :id="'tracking_' + order.id" class="bg-white text-xs rounded p-2" value="">
                            </div>
                            <div class="flex items-center space-x-2">
                                <label :for="'responsible_' + order.id" class="text-gray-700">Phụ trách:</label>
                                <select :id="'responsible_' + order.id" class="bg-white text-xs rounded py-2 px-8">
                                    <option value="">Chọn</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                        {{ (Auth::check() && Auth::user()->id == $user->id) }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center space-x-2">
                                <label :for="'notes_' + order.id" class="text-gray-700">Ghi chú:</label>
                                <input type="text" :id="'notes_' + order.id" class="bg-white text-xs rounded p-2" value="">
                            </div>
                            <div>
                                <button class="bg-green-600 hover:bg-green-800 text-white text-xs font-bold py-2 px-4 rounded mt-5 md:mt-0" @click="sendOrder(order.id)">Cập nhật</button>
                            </div>
                            <div class="flex items-center space-x-2">
                                <label :for="'platform_' + order.id" class="text-gray-700">Kênh BH:</label>
                                <select :id="'platform_' + order.id" class="bg-white text-xs rounded py-2 px-6">
                                    @foreach($platforms as $platform)
                                        <option value="{{ $platform->id }}">
                                            {{ $platform->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="flex items-center space-x-2">
                                <!-- Template cho thanh toán -->
                                <template x-for="finance in order.finances" :key="finance.id">
                                    <table class="min-w-full leading-normal mb-4">
                                        <thead>
                                            <tr>
                                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Cần trả
                                                </th>
                                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Đã trả
                                                </th>
                                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Còn lại
                                                </th>
                                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Ngày tạo
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap" x-text="finance.amount_due"></p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap" x-text="finance.amount_paid"></p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap" x-text="finance.amount_remaining"></p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap" x-text="finance.created_at"></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </template>
                            </div>
                            <div class="flex items-center space-x-2">
                                <label :for="'notes_' + order.id" class="text-gray-700">Thanh toán:</label>
                                <input type="number" :id="'notes_' + order.id" class="bg-white text-xs rounded p-2" value="">
                                <button @click="editDetail(detail)" class="text-xs bg-blue-500 text-white p-1 rounded">Thêm thanh toán</button>
                            </div>
                        </div>
                        
                        <table class="min-w-full bg-gray-100 border border-gray-200 rounded-lg">
                            <thead class="bg-blue-500 text-white text-sm rounded-lg">
                                <tr>
                                    <th class="py-2 px-2 text-left font-normal">STT</th>    
                                    <th class="py-2 px-1 text-left font-normal">SKU</th>
                                    <th class="py-2 px-1 text-left font-normal">Tên Sản phẩm</th>
                                    <th class="py-2 px-1 text-left font-normal">SL</th>
                                    <th class="py-2 px-1 text-left font-normal">Giá</th>
                                    <th class="py-2 px-1 text-left font-normal">%CK</th>
                                    <th class="py-2 px-1 text-left font-normal">CK</th>
                                    <th class="py-2 px-1 text-left font-normal">Tổng</th>
                                    <th class="py-2 px-1 text-left font-normal">CTKM</th>
                                    <th class="py-2 px-1 text-center font-normal">Bộ</th>
                                    <th class="py-2 px-1 text-center font-normal">Hủy</th>
                                    <th class="py-2 px-1 text-center font-normal">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="detail in order.details" :key="detail.id">
                                    <tr class="border-t border-gray-200">
                                        <td class="py-2 px-2 w-1/24" x-text="detail.product.product_api_id"></td>
                                        <td class="py-2 px-2 w-3/24" x-text="detail.product.sku"></td>
                                        <td class="py-2 px-2 w-9/24" >
                                            <div x-text="detail.product.name" class="text-xs"></div>
                                            <div>
                                                <input x-model="detail.notes" class="text-xs p-1 border-none rounded w-full bg-gray-50 italic" type="text" @change="updateNotes(detail)">
                                            </div>
                                        </td>
                                        <td class="py-2 px-2 w-1/24">
                                            <input x-model="detail.quantity" class="text-xs p-1 border rounded w-16 ml-2" type="number" min="1" @change="updateQuantity(detail)">
                                        </td>
                                        <td class="py-2 px-2 w-1/24" x-text="detail.product.price"></td>
                                        <td class="py-2 px-2 w-1/24">
                                            <input x-model="detail.discount_percent" class="text-xs p-1 border rounded w-16 ml-2" type="number" min="0.00" @change="updateDiscountPercent(detail)">
                                        </td>
                                        <td class="py-2 px-2 w-1/24">
                                            <input x-model="detail.discount" class="text-xs p-1 border rounded w-16 ml-2" type="number" min="1" @change="updateDiscount(detail)">
                                        </td>
                                        <td class="py-2 px-2 w-2/24" x-text="detail.total"></td>
                                        <td class="py-2 px-2 w-1/24" x-text="detail.promotion_id"></td>
                                        <td class="py-2 px-2 w-1/24" x-text="detail.bundle_id"></td>
                                        <td class="py-2 px-2 w-1/24">
                                            <input x-model="detail.is_cancelled" type="checkbox" class="text-xs p-1 border rounded w-4 ml-2" @change="updateCancelled(detail)">
                                        </td>
                                        <td class="py-2 px-2 w-1/24">
                                            <!-- <button @click="editDetail(detail)" class="text-xs bg-blue-500 text-white p-1 rounded">🖉</button> -->
                                            <button @click="deleteDetail(detail)" class="text-xs bg-red-500 text-white p-1 rounded px-2">x</button>
                                        </td>
                                    </tr>
                                </template>
                                <tr>
                                    <td colspan="10" class="py-2 px-2 text-left">
                                        <button @click="addDetail()" class="bg-green-500 text-white px-4 py-2 rounded">Thêm mới</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="flex flex-col space-y-2 mt-4">
                            <div class="flex justify-end items-center space-x-4">
                                <label :for="'discount_percent_' + order.id" class="text-gray-700">Phần trăm giảm giá:</label>
                                <input type="text" :id="'discount_percent_' + order.id" class="bg-gray-50 text-xs rounded p-1" x-model="order.discount_percent">
                            </div>
                            <div class="flex justify-end items-center space-x-4">
                                <label :for="'total_discount_' + order.id" class="text-gray-700">Tổng giảm giá:</label>
                                <input type="text" :id="'total_discount_' + order.id" class="bg-gray-50 text-xs rounded p-1" x-model="order.total_discount">
                            </div>
                            <div class="flex justify-end items-center space-x-4">
                                <label :for="'tax_' + order.id" class="text-gray-700">Thuế:</label>
                                <input type="text" :id="'tax_' + order.id" class="bg-gray-50 text-xs rounded p-1" x-model="order.tax">
                            </div>
                            <div class="flex justify-end items-center space-x-4">
                                <label :for="'shipping_fee_' + order.id" class="text-gray-700">Phí vận chuyển:</label>
                                <input type="text" :id="'shipping_fee_' + order.id" class="bg-gray-50 text-xs rounded p-1" x-model="order.shipping_fee">
                            </div>
                            <div class="flex justify-end items-center space-x-4">
                                <label :for="'customer_shipping_fee_' + order.id" class="text-gray-700">Phí vận chuyển khách hàng:</label>
                                <input type="text" :id="'customer_shipping_fee_' + order.id" class="bg-gray-50 text-xs rounded p-1" x-model="order.customer_shipping_fee">
                            </div>
                            <div class="flex justify-end items-center space-x-4">
                                <label class="text-gray-700">Tổng số tiền:</label>
                                <div class="text-xs p-1" x-text="order.total_amount"></div>
                            </div>
                        </div>

                    </td>
                </tr>
            </template>
        </tbody>
    </template>


</table>
</div>
