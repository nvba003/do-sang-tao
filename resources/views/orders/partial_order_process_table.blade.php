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
                <th scope="col" class="w-2/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Mã vận chuyển</th>
                <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Trạng thái</th>
                <th scope="col" class="w-5/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Tình trạng</th>
                <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Ngày XL tiếp</th>
                <th scope="col" class="w-2/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Kênh</th>
                <th scope="col" class="w-1/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Hủy/Hoàn</th>
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
                <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="order.order.order_code"></td>
                <td class="w-4/24 px-6 py-4 whitespace-nowrap" x-text="order.order.customer_account.account_name"></td>
                <td class="w-2/24 px-6 py-4 whitespace-nowrap" x-text="order.tracking_number"></td>
                <td class="w-3/24 px-6 py-4 whitespace-nowrap" x-text="getStatus(order.status_id)"></td>
                <td class="w-5/24 px-6 py-4 whitespace-nowrap" x-text="order.condition ? order.condition.name : '-'"></td>
                <td class="w-3/24 px-6 py-4 whitespace-nowrap" x-text="order.processing_date"></td>
                <td class="w-2/24 px-6 py-4 whitespace-nowrap" x-text="order.platform.name"></td>
                <td class="w-1/24 px-6 py-4 whitespace-nowrap" x-text="order.cancel_return_id"></td>
            </tr>
            <template x-if="order.showDetails">
                <tr class="bg-blue-100" x-init="$nextTick(() => {
                    flatpickr('.datepicker', {
                        dateFormat: 'Y-m-d'  // Định dạng chỉ ngày
                    });

                    flatpickr('.timepicker', {
                        enableTime: true,  // Kích hoạt chọn thời gian
                        dateFormat: 'Y-m-d H:i',  // Định dạng cho ngày giờ
                    });
                })">
                    <td colspan="100%" class="px-2 sm:px-4 py-2 text-xs md:text-base">
                        <div class="flex items-center space-x-4 mb-2">
                            <button class="bg-green-600 hover:bg-green-800 text-white text-xs font-bold py-2 px-4 rounded mt-5 md:mt-0" @click="sendOrder(order.id)">Cập nhật</button>
                            <button class="bg-blue-600 hover:bg-blue-800 text-white text-xs font-bold py-2 px-4 rounded mt-5 md:mt-0" @click="order.showCancelAndReturn = !order.showCancelAndReturn">Hiện/Ẩn Hủy/Trả</button>
                        </div>
                        <!-- Template cho thông tin hủy và trả hàng -->
                        <template x-if="order.showCancelAndReturn">
                            <table class="min-w-full leading-normal mb-4">
                                <thead>
                                    <tr>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Loại
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Nguyên nhân
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Ngày hoàn NVC
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Ghi chú
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap" x-text="order.cancelAndReturn.type"></p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap" x-text="order.cancelAndReturn.reason"></p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap" x-text="order.cancelAndReturn.carrier_return_date"></p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap" x-text="order.cancelAndReturn.notes"></p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>

                        <table class="min-w-full bg-white shadow overflow-hidden sm:rounded-lg">
                            <!-- Thông tin cơ bản -->
                            <thead class="bg-gray-200">
                                <tr>
                                    <th colspan="100%" class="px-6 py-3 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">
                                        Thông Tin Chung
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b">
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Trạng thái:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500" x-text="order.status_id"></td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Tình trạng:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500" x-text="order.order_condition_id"></td>
                                <!-- </tr>
                                <tr> -->
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Người phụ trách:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500" x-text="order.responsible_user_id">
                                        <option value="">Chọn</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                {{ (Auth::check() && Auth::user()->id == $user->id) }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Chi nhánh:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500" x-text="order.branch_id"></td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Ghi chú:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500" x-text="order.status_id">
                                        <input type="text" :id="'notes_' + order.id" class="bg-white text-xs rounded p-2" value="">
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Kết quả:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500" x-text="order.status_id">
                                        <input type="text" :id="'notes_' + order.id" class="bg-white text-xs rounded p-2" value="">
                                    </td>
                                </tr>
                            </tbody>

                            <!-- Thông tin vận chuyển -->
                            <thead class="bg-gray-200">
                                <tr>
                                    <th colspan="100%" class="px-6 py-3 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">
                                        Thông Tin Vận Chuyển
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Nhà vận chuyển:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500" x-text="order.carrier.name"></td>
                                <!-- </tr>
                                <tr> -->
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Ngày vận chuyển:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500" x-text="order.ship_date"></td>
                                <!-- </tr>
                                <tr> -->
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Ngày giao hàng ước tính:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                        <input type="text" class="datepicker text-xs p-1 border rounded w-full" x-model="order.estimated_delivery_date" @change="updateApprovalTime(order)">
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Ngày giao hàng thực tế:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500" x-text="order.actual_delivery_date"></td>
                                </tr>
                            </tbody>

                            <!-- Thông tin xử lý -->
                            <thead class="bg-gray-200">
                                <tr>
                                    <th colspan="100%" class="px-6 py-3 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">
                                        Thông Tin Xử Lý
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Ngày duyệt:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                        <input type="text" class="timepicker text-xs p-1 border rounded w-full" x-model="order.approval_time" @change="updateApprovalTime(order)">
                                    </td>
                                <!-- </tr>
                                <tr> -->
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Ngày đóng gói:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500" x-text="order.packing_time"></td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Giao bưu tá:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500" x-text="order.delivery_handoff_time"></td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Ngày hoàn thành:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500" x-text="order.completion_time"></td>

                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-800">Ngày nhận hủy/hoàn:</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500" x-text="order.received_return_date"></td>
                                </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>
            </template>
        </tbody>
    </template>


</table>
</div>
