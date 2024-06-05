
    <!-- <button @click="updateOrders" class="bg-green-500 text-white px-4 py-2 rounded mb-4">Update orders</button> -->
    <table class="w-full bg-white border border-gray-200 rounded-lg rounded">
        <thead class="text-white bg-gray-500">
            <tr>
                <th scope="col" class="w-1/24 px-2 py-2 mt-1 text-center text-xs md:text-sm hidden sm:block font-semibold uppercase tracking-wider">
                    <input type="checkbox" x-model="checkAll" @click="toggleAll">
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
        <tr :class="{
            'bg-red-200': order.cancel_and_return !== null,
            'bg-green-500': order.cancel_and_return == null && order.completion_time !== null, 
            'bg-blue-200': order.cancel_and_return == null && order.showDetails && order.completion_time === null, 
            'bg-white': order.cancel_and_return == null && !order.showDetails && order.completion_time === null
        }" class="border-b">
                <td class="p-2 w-1/24 text-center mt-2 hidden sm:block">
                    <input type="checkbox" class="checkItem" :value="order.id" x-model="selectedItems" @change="updateCount">
                </td>
                <td class="p-2 w-1/24 whitespace-nowrap">
                    <button @click="order.showDetails = !order.showDetails" x-text="order.showDetails ? '-' : '+'" class="bg-blue-500 w-6 h-6 text-white rounded"></button>
                </td>
                <td class="w-4/24 px-2 py-3 w-3/24 whitespace-nowrap" x-text="order.order.order_code"></td>
                <td class="w-4/24 px-2 py-3 w-3/24 whitespace-nowrap" x-text="order.order.customer_account.account_name"></td>
                <td class="w-2/24 px-2 py-3 w-3/24 whitespace-nowrap" x-text="order.tracking_number"></td>
                <td class="w-3/24 px-2 py-3 w-3/24 whitespace-nowrap" x-text="getStatus(order.status_id)"></td>
                <td class="w-5/24 px-2 py-3 w-3/24 whitespace-nowrap" x-text="getOrderConditionName(order.order_condition_id)"></td>
                <td class="w-3/24 px-2 py-3 w-3/24 whitespace-nowrap" x-text="order.processing_date"></td>
                <td class="w-2/24 px-2 py-3 w-3/24 whitespace-nowrap" x-text="order.platform.name"></td>
                <td class="w-1/24 px-2 py-3 whitespace-nowrap"
                    x-text="order.cancel_and_return ? (order.received_return_date ? 'Nhận hoàn' : (order.cancel_and_return.carrier_return_date ? 'Chưa nhận' : 'Đang hoàn')) : ''"
                    :class="{
                        'text-blue-500': order.received_return_date,
                        'text-yellow-600': !order.received_return_date && order.cancel_and_return ? !order.cancel_and_return.carrier_return_date : '',
                        'text-red-500': order.cancel_and_return &&  order.cancel_and_return ? order.cancel_and_return.carrier_return_date : '' && !order.received_return_date
                    }">
                </td>
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
                            <div x-data="{ 
                                    userId: '{{ auth()->user()->id }}',
                                    entry: {}, 
                                    init() {
                                        this.entry = order.cancel_and_return || {processed_by: '', type: '', reason: '', carrier_return_date: '', notes: ''};
                                    }
                                }" x-init="init" class="w-full rounded">
                                <button class="bg-green-600 hover:bg-green-800 text-white text-xs font-medium py-2 px-4 rounded mt-2 md:mt-0" @click="updateOrder(order)">Cập nhật</button>
                                <template x-if="!order.cancel_and_return">
                                    <button @click="entry = { processed_by: userId, type: '1', reason: '', carrier_return_date: '', notes: '' }; order.cancel_and_return = entry" class="bg-blue-600 hover:bg-blue-800 text-white text-xs font-medium py-2 px-4 rounded ml-3 mt-2 md:mt-0">
                                        Thêm mới hoàn/trả
                                    </button>
                                </template>
                                <template x-if="order.cancel_and_return">
                                    <table class="w-full bg-gray-100 shadow rounded my-2">
                                        <thead class="w-full border-b-2 border-gray-200 bg-gray-300 text-xs font-semibold text-gray-600 uppercase">
                                            <tr>
                                                <th class="px-2 py-3">Phụ trách</th>
                                                <th class="px-2 py-3">Loại</th>
                                                <th class="px-2 py-3">Lý do</th>
                                                <th class="px-2 py-3 max-w-24">Ngày NVC báo hoàn</th>
                                                <th class="px-2 py-3">Ghi chú</th>
                                                <th class="px-2 py-3">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="px-2 py-2 border-b border-gray-200 text-xs">
                                                    <select x-model="entry.processed_by" class="form-select rounded-md shadow-sm mt-1 block w-full text-xs">
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-2 py-2 border-b border-gray-200 text-xs">
                                                    <select x-model="entry.type" class="form-select rounded-md shadow-sm mt-1 block w-full text-xs">
                                                        <option value="0">Hủy</option>
                                                        <option value="1">Trả</option>
                                                    </select>
                                                </td>
                                                <td class="px-2 py-2 border-b border-gray-200 text-xs">
                                                    <select x-model="entry.reason" class="form-select rounded-md shadow-sm mt-1 block w-full text-xs">
                                                        <option value="">Chọn</option>
                                                        @foreach($cancelReturnReasons as $cancelReturnReason)
                                                            <option value="{{ $cancelReturnReason->id }}">{{ $cancelReturnReason->reason }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-2 py-2 border-b border-gray-200 text-xs max-w-24">
                                                    <input x-model="entry.carrier_return_date" class="rounded-md shadow-sm mt-1 block w-full text-xs" type="date" />
                                                </td>
                                                <td class="px-2 py-2 border-b border-gray-200 text-xs">
                                                    <textarea x-model="entry.notes" class="rounded-md shadow-sm mt-1 block w-full text-xs" rows="1"></textarea>
                                                </td>
                                                <td class="px-2 py-2 border-b border-gray-200 text-xs text-center">
                                                    <button @click="saveCancelReturn(order.cancel_and_return, order.id)" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                        Lưu
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </template>
                            </div>
                        </div>

                        <table class="w-full bg-white shadow overflow-hidden sm:rounded-lg">
                            <!-- Thông tin cơ bản -->
                            <thead class="bg-gray-200">
                                <tr>
                                    <th colspan="100%" class="px-6 py-3 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">
                                        Thông Tin Chung
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b w-full">
                                    <td colspan="1" class="px-2 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Trạng thái:</td>
                                    <td colspan="1" class="px-2 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-700">
                                        <select :id="'orderStatus_' + order.id" class="bg-white text-xs rounded py-2 px-6 w-full" x-model="order.status_id">
                                            <option value="">Chọn</option>
                                            @foreach($orderStatuses as $orderStatus)
                                                <option value="{{ $orderStatus->id }}">{{ $orderStatus->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td colspan="1" class="px-2 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Tình trạng:</td>
                                    <td colspan="2" class="px-2 py-3 w-6/24 whitespace-no-wrap text-sm leading-5 text-gray-700">
                                        <select :id="'orderCondition_' + order.id" class="bg-white text-xs rounded py-2 px-6 w-full" x-model="order.order_condition_id">
                                            <option value="">Chọn</option>
                                            @foreach($orderConditions as $orderCondition)
                                                <option value="{{ $orderCondition->id }}">{{ $orderCondition->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td colspan="1" class="px-2 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Người phụ trách:</td>
                                    <td colspan="1" class="px-2 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-700">
                                        <select :id="'responsible_' + order.id" class="bg-white text-xs rounded py-2 px-8 w-full" x-model="order.responsible_user_id">
                                            <option value="">Chọn</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td colspan="1" class="px-2 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Ngày XL tiếp:</td>
                                    <td colspan="1" class="px-2 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-700">
                                        <input type="date" class="text-xs p-1 border rounded w-full" x-model="order.processing_date">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1" class="px-2 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Ghi chú:</td>
                                    <td colspan="3" class="px-2 py-3 whitespace-no-wrap text-sm leading-5 text-gray-700">
                                        <textarea :id="'notes_' + order.id" class="bg-white text-xs rounded p-2 w-full" x-model="order.notes" rows="1"></textarea>
                                    </td>
                                    <td colspan="1" class="px-2 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Kết quả:</td>
                                    <td colspan="3" class="px-2 py-3 whitespace-no-wrap text-sm leading-5 text-gray-700">
                                        <input type="text" :id="'result_' + order.id" class="bg-white text-xs rounded p-2 w-full" x-model="order.result">
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
                                    <td class="px-0 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Nhà vận chuyển:</td>
                                    <td class="px-1 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-700" x-text="order.carrier.name"></td>
                                    <td class="px-0 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Ngày vận chuyển:</td>
                                    <td class="px-1 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-700">
                                        <input type="date" class="text-xs p-1 border rounded w-full" x-model="order.ship_date">
                                    </td>
                                    <td class="px-0 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Ngày nhận ước tính:</td>
                                    <td class="px-1 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-700">
                                        <input type="date" class="text-xs p-1 border rounded w-full" x-model="order.estimated_delivery_date">
                                    </td>
                                    <td class="px-0 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Ngày nhận thực tế:</td>
                                    <td class="px-1 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-700">
                                        <input type="date" class="text-xs p-1 border rounded w-full" x-model="order.actual_delivery_date">
                                    </td>
                                </tr>
                            </tbody>

                            <!-- Thông tin xử lý -->
                            <thead class="bg-gray-200">
                                <tr>
                                    <th colspan="100%" class="px-6 py-3 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">
                                        Thông Tin Xử Lý Theo Ngày
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-2 py-3 w-1/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Duyệt:</td>
                                    <td class="px-2 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-700">
                                        <input type="text" class="timepicker text-xs p-1 border rounded w-full" x-model="order.approval_time">
                                    </td>
                                    <td class="px-2 py-3 w-1/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Đóng gói:</td>
                                    <td class="px-2 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-700">
                                        <input type="text" class="timepicker text-xs p-1 border rounded w-full" x-model="order.packing_time">
                                    </td>
                                    <td class="px-2 py-3 w-2/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Giao bưu tá:</td>
                                    <td class="px-2 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-700">
                                        <input type="text" class="timepicker text-xs p-1 border rounded w-full" x-model="order.delivery_handoff_time">
                                    </td>
                                    <td class="px-2 py-3 w-2/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Hoàn thành:</td>
                                    <td class="px-2 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-700">
                                        <input type="date" class="text-xs p-1 border rounded w-full" x-model="order.completion_time">
                                    </td>
                                    <td class="px-2 py-3 w-2/24 whitespace-no-wrap text-sm leading-5 text-gray-900 text-right font-medium">Nhận hoàn:</td>
                                    <td class="px-2 py-3 w-3/24 whitespace-no-wrap text-sm leading-5 text-gray-700">
                                        <input type="date" class="text-xs p-1 border rounded w-full" x-model="order.received_return_date">
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>
            </template>
        </tbody>
    </template>


</table>

