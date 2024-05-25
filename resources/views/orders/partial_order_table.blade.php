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
                <th scope="col" class="w-4/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Trạng thái</th>
                <th scope="col" class="w-4/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Phụ trách</th>
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
                <td class="w-5/24 px-6 py-4 whitespace-nowrap" x-text="order.order_process ? order.order_process.user.name : '_'"></td>
                <!-- <td class="w-5/24 px-6 py-4 whitespace-nowrap" x-text="order.customer_account.customer_id"></td> -->
                <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="order.platform.name"></td>
            </tr>
            <template x-if="order.showDetails">
                <tr class="bg-blue-100 border-b">
                    <td colspan="100%" class="text-xs md:text-base">
                        <div class="flex flex-col w-full">
                            <div class="flex w-full mb-2">
                                <div class="flex-1 p-4 m-2 bg-gray-50 rounded-lg shadow">
                                    <div class="flex flex-wrap -mx-2">
                                        <!-- NVC: -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'carrier_' + order.id" class="text-gray-700 text-xs">NVC:</label>
                                            <select :id="'carrier_' + order.id" class="bg-white text-xs rounded py-2 px-6 w-full" x-model="order.order_process.carrier_id">
                                                <option value="">Chọn</option>
                                                @foreach($carriers as $carrier)
                                                    <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Phụ trách: -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'responsible_' + order.id" class="text-gray-700 text-xs">Phụ trách:</label>
                                            <select :id="'responsible_' + order.id" class="bg-white text-xs rounded py-2 px-8 w-full" x-model="order.order_process.responsible_user_id">
                                                <option value="">Chọn</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Mã KH: -->
                                        <div x-data="autocompleteSetup(order.customer_account.customer_id)" x-init="initAutocomplete" class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'customer_' + order.id" class="text-gray-700 text-xs">Mã KH:</label>
                                            <input :id="'customer_' + order.id" x-ref="customerInput" type="text" class="bg-white text-xs rounded p-2 w-full"  x-model="selectedCustomerLabel">
                                        </div>
                                        <!-- Vận đơn: -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'tracking_' + order.id" class="text-gray-700 text-xs">Vận đơn:</label>
                                            <input :id="'tracking_' + order.id" type="text" class="bg-white text-xs rounded p-2 w-full" x-model="order.order_process.tracking_number">
                                        </div>
                                        <!-- Loại đơn: -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'orderType_' + order.id" class="text-gray-700 text-xs">Loại đơn:</label>
                                            <select :id="'orderType_' + order.id" class="bg-white text-xs rounded py-2 px-6 w-full" x-model="order.order_type_id">
                                                <option value="">Chọn</option>
                                                @foreach($orderTypes as $orderType)
                                                    <option value="{{ $orderType->id }}">{{ $orderType->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Nguồn đơn từ: -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'source_' + order.id" class="text-gray-700 text-xs">Nguồn đơn từ:</label>
                                            <input :id="'source_' + order.id" type="text" class="bg-white text-xs rounded p-2 w-full" x-model="order.source_info">
                                        </div>
                                        <!-- Trạng thái: -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'orderStatus_' + order.id" class="text-gray-700 text-xs">Trạng thái:</label>
                                            <select :id="'orderStatus_' + order.id" class="bg-white text-xs rounded py-2 px-6 w-full" x-model="order.status_id">
                                                <option value="">Chọn</option>
                                                @foreach($orderStatuses as $orderStatus)
                                                    <option value="{{ $orderStatus->id }}">{{ $orderStatus->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Ghi chú: -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'notes_' + order.id" class="text-gray-700 text-xs">Ghi chú:</label>
                                            <textarea :id="'notes_' + order.id" class="bg-white text-xs rounded p-2 w-full" x-model="order.notes" rows="2"></textarea>
                                        </div>
                                        <!-- Button Update -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2 mt-4">
                                            <button class="bg-green-600 hover:bg-green-800 text-white text-xs font-bold py-2 px-4 rounded w-full" @click="updateOrder(order)">Cập nhật</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="flex-1 p-4 m-2 bg-gray-50 rounded-lg shadow">
                                    <div class="items-center space-y-4 mb-4">
                                        <div class="flex items-center space-x-2">
                                            <label :for="'notes_' + order.id" class="text-gray-700">Thanh toán:</label>
                                            <input type="number" :id="'notes_' + order.id" class="bg-white text-xs rounded p-2" value="">
                                            <button @click="editDetail(detail)" class="text-xs bg-blue-500 text-white p-1 rounded">Thêm thanh toán</button>
                                        </div>
                                        <div class="flex items-center space-x-2">
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
                                    </div>
                                </div> -->
                                <div class="flex-1 p-4 m-2 bg-gray-50 rounded-lg shadow">
                                    <div class="space-y-2">
                                        <div class="flex items-center space-x-2">
                                            <label for="'notes_' + order.id" class="text-gray-700 text-sm font-medium">Thanh toán:</label>
                                            <input type="number" id="'notes_' + order.id" class="bg-white text-sm rounded p-2 border-gray-300" value="">
                                            <button @click="editDetail(detail)" class="text-sm bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">Thêm</button>
                                            <button @click="editDetail(detail)" class="text-sm bg-green-500 hover:bg-blue-700 text-white ml-2 py-2 px-4 rounded">Thanh toán đủ</button>
                                        </div>
                                        <div>
                                            <!-- Template cho thanh toán -->
                                            <template x-for="finance in order.finances" :key="finance.id">
                                                <table class="min-w-full leading-normal overflow-y-auto">
                                                    <thead>
                                                        <tr class="bg-gray-100">
                                                            <th class="px-5 py-2 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                                Cần trả
                                                            </th>
                                                            <th class="px-5 py-2 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                                Đã trả
                                                            </th>
                                                            <th class="px-5 py-2 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                                Còn lại
                                                            </th>
                                                            <th class="px-5 py-2 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                                Ngày tạo
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="px-5 py-2 border-b border-gray-200 bg-white text-sm">
                                                                <p class="text-gray-900 whitespace-no-wrap" x-text="finance.amount_due"></p>
                                                            </td>
                                                            <td class="px-5 py-2 border-b border-gray-200 bg-white text-sm">
                                                                <p class="text-gray-900 whitespace-no-wrap" x-text="finance.amount_paid"></p>
                                                            </td>
                                                            <td class="px-5 py-2 border-b border-gray-200 bg-white text-sm">
                                                                <p class="text-gray-900 whitespace-no-wrap" x-text="finance.amount_remaining"></p>
                                                            </td>
                                                            <td class="px-5 py-2 border-b border-gray-200 bg-white text-sm">
                                                                <p class="text-gray-900 whitespace-no-wrap" x-text="finance.created_at"></p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full p-2 rounded-lg">
                                <table class="w-full bg-gray-100 border border-gray-200 rounded-lg">
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
                                                <td class="py-2 px-2 w-3/24" x-data="autocompleteProductSetup(detail)" x-init="initAutocompleteProduct">
                                                    <input :id="'product_' + detail.id" x-ref="productInput" type="text" class="bg-white text-xs rounded p-2 w-full"
                                                        x-model="detail.product.sku">
                                                </td>
                                                <!-- <td class="py-2 px-2 w-3/24" x-text="detail.product.sku"></td> -->
                                                <td class="py-2 px-2 w-9/24" >
                                                    <div x-text="detail.product.name" class="text-xs"></div>
                                                    <div>
                                                        <input x-model="detail.notes" class="text-xs p-1 border-none rounded w-full bg-gray-50 italic" type="text" @change="updateNotes(detail)">
                                                    </div>
                                                </td>
                                                <td class="py-2 px-2 w-1/24">
                                                    <input x-model="detail.quantity" class="text-xs p-1 border rounded w-16 ml-2" type="number" min="1" @change="updateQuantity(detail)">
                                                </td>
                                                <td class="py-2 px-2 w-1/24">
                                                    <input x-model="detail.price" class="text-xs p-1 border rounded w-16 ml-2" type="number" min="1">
                                                </td>
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
                                                    <button @click="deleteDetail(detail)" class="text-xs bg-red-500 text-white p-1 rounded px-2">x</button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr>
                                            <td colspan="9" class="py-2 px-2 text-left">
                                                <button @click="addDetail()" class="bg-blue-500 text-white px-4 py-2 mx-2 rounded">Thêm mới</button>
                                                <button @click="addPackingDetail()" class="bg-yellow-500 text-white px-4 py-2 mx-2 rounded">Chuyển đóng gói</button>
                                                
                                            </td>
                                            <td colspan="3" class="py-2 px-2 text-right">
                                                <button @click="updateDetail()" class="bg-green-500 text-white px-4 py-2 rounded">Cập nhật</button>
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
                                        <label :for="'customer_shipping_fee_' + order.id" class="text-gray-700">Phí vận chuyển khách trả:</label>
                                        <input type="text" :id="'customer_shipping_fee_' + order.id" class="bg-gray-50 text-xs rounded p-1" x-model="order.customer_shipping_fee">
                                    </div>
                                    <div class="flex justify-end items-center space-x-4">
                                        <label class="text-gray-700">Tổng số tiền:</label>
                                        <input type="text" :id="'total_amount_' + order.id" class="bg-gray-200 text-xs rounded p-1" x-model="order.total_amount" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </template>
        </tbody>
    </template>


</table>
</div>
