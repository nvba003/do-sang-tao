@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<x-conditional-content :condition="auth()->user()->hasRole('admin') || auth()->user()->hasRole('orderProcess')">
<div class="container mx-auto px-2 sm:px-3 lg:px-4">
    <div id="orderTable" class="w-full" x-data="orderTable()">
        <div class="flex flex-wrap mx-auto mt-2 p-4 bg-white rounded shadow-md mb-2">
            <form id="searchOrder" @submit.prevent="submitForm" class="w-full mb-1">
                <div class="flex flex-wrap -mx-2">
                    <!-- Tìm đơn hàng -->
                    <div class="w-full sm:w-1/4 md:w-3/12 xl:w-4/24 px-2 mb-2 md:mb-0">
                        <label for="searchOrderCode" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Tìm đơn hàng:</label>
                        <div class="relative">
                            <input type="text" id="searchOrderCode" x-model="searchParams.searchOrderCode" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập mã đơn hàng">
                            <div class="absolute inset-y-0 right-0 flex items-center px-1">
                                <button type="button" @click="searchParams.searchOrderCode = ''" class="bg-gray-200 hover:bg-gray-300 text-gray-500 text-sm p-2 rounded-r-md">Xóa</button>
                            </div>
                        </div>
                    </div>
                    <!-- Tìm khách hàng -->
                    <div class="w-full sm:w-1/4 md:w-3/12 xl:w-3/24 px-2 mb-2 md:mb-0">
                        <label for="searchCustomer" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Tìm khách hàng:</label>
                        <div class="relative">
                            <input type="text" id="searchCustomer" x-model="searchParams.searchCustomer" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập tên khách hàng">
                            <div class="absolute inset-y-0 right-0 flex items-center px-1">
                                <button type="button" @click="searchParams.searchCustomer = ''" class="bg-gray-200 hover:bg-gray-300 text-gray-500 text-sm p-2 rounded-r-md">Xóa</button>
                            </div>
                        </div>
                    </div>
                    <!-- Ngày XL từ -->
                    <div class="w-full sm:w-1/4 md:w-2/12 xl:w-2/24 px-0 mx-2 mb-2 md:mb-0">
                        <label for="searchCreatedAtFrom" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Ngày XL từ:</label>
                        <input type="date" id="searchCreatedAtFrom" x-model="searchParams.searchCreatedAtFrom" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <!-- Ngày XL đến -->
                    <div class="w-full sm:w-1/4 md:w-2/12 xl:w-2/24 px-0 mx-2 mb-2 md:mb-0">
                        <label for="searchCreatedAtTo" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Ngày XL đến:</label>
                        <input type="date" id="searchCreatedAtTo" x-model="searchParams.searchCreatedAtTo" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <!-- Trạng thái -->
                    <div class="w-full sm:w-1/4 md:w-2/12 xl:w-2/24 px-2 mb-1 md:mb-0">
                        <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Trạng thái:</label>
                        <select id="status" x-model="searchParams.status" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            @foreach($orderStatuses as $orderStatus)
                                <option value="{{ $orderStatus->id }}">{{ $orderStatus->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Hoàn thành -->
                    <div class="w-full sm:w-1/4 md:w-2/12 xl:w-3/24 px-2 mb-1 md:mb-0">
                        <label for="completion" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Ngày H.Thành:</label>
                        <select id="completion" x-model="searchParams.completion" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            <option value="0">Chưa hoàn thành</option>
                            <option value="1">Đã hoàn thành</option>
                        </select>
                    </div>
                    <!-- Đơn hoàn -->
                    <div class="w-full sm:w-1/4 md:w-2/12 xl:w-2/24 px-2 mb-1 md:mb-0">
                        <label for="return" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Nhận Hoàn:</label>
                        <select id="return" x-model="searchParams.return" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            <option value="0">Chưa nhận</option>
                            <option value="1">Đã nhận</option>
                        </select>
                    </div>
                    <!-- Kênh bán hàng -->
                    <div class="w-full sm:w-1/4 md:w-2/12 xl:w-2/24 px-2 mb-1 md:mb-0">
                        <label for="platform" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Kênh:</label>
                        <select id="platform" x-model="searchParams.platform" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            @foreach($platforms as $platform)
                                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Nút Tìm -->
                    <div class="w-full sm:w-1/4 md:w-2/12 xl:w-2/24 mt-2 px-2 flex items-end">
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 w-full">Tìm</button>
                    </div>
                </div>
            </form>


        </div>
        @include('orders.partial_order_process_table', compact(
            'branch_id',
            'users',
            'carriers',
            'platforms',
            'orderStatuses',
            'orderConditions',
            'cancelReturnReasons'
        ))
        <div class="mx-auto mt-2 max-w-full">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 m-2">
                <div class="lg:col-span-3 md:col-span-1"></div>
                <nav class="lg:col-span-7 col-span-1 md:col-span-9 pagination" x-html="links"></nav>
                <div class="lg:col-span-2 col-span-1 md:col-span-2 justify-end">
                    <div class="flex items-center space-x-2">
                        <label for="perPage" class="text-sm flex-grow text-right pr-2">Số hàng:</label>
                        <select x-model="perPage" @change="fetchData(urls.baseUrl)" class="px-1 py-2 text-sm w-20">
                            <option value="15">15</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal thông báo -->
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="successModal" style="display: none;">
  <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
    <!-- Modal Header -->
    <div class="flex justify-between items-center pb-3">
      <p class="text-2xl font-bold">Thành công!</p>
      <div class="modal-close cursor-pointer z-50" onclick="toggleModal(false)">
        <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
          <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"/>
        </svg>
      </div>
    </div>
    <!-- Modal Body -->
    <div class="text-sm">
      Thao tác thành công!
    </div>
  </div>
</div>
</x-conditional-content>
@endsection

@push('scripts')
<script>
    window.onload = function() {
        @if(session('success'))
            toggleModal(true); // Hiển thị modal khi có thông báo thành công
            setTimeout(function() {
                toggleModal(false); // Ẩn modal sau 500ms
            }, 500);
        @endif
    };
    function toggleModal(show) {
        const modal = document.getElementById('successModal');
        modal.style.display = show ? 'block' : 'none';
    }

    const branchId = {{ $branch_id }};// Truyền giá trị branch_id từ Blade vào JavaScript
    const urls = {
        baseUrl: `{{ url('/order-process') }}/${branchId}`
    };

    document.addEventListener('alpine:init', () => {
        Alpine.data('orderTable', () => ({
            // order: {
            //     details: []
            // },
            orders: [],
            currentPage: 1,  // Ensure currentPage is part of your data model
            lastPage: 1,
            perPage: 15,
            links: '',
            searchParams: {
                searchOrderCode: '',
                searchCustomer: '',
                searchCreatedAtFrom: '',
                searchCreatedAtTo: '',
                status: '',
                completion: '',
                return: '',
                platform: '',
            },
            selectedItems: [],
            checkAll: false,
            selectedCount: 0,
            orderConditions: @json($orderConditions),
            toggleModal(detail) {
                detail.openModal = !detail.openModal;
            },
            toggleAll() {
                if (!this.checkAll) {
                    this.selectedItems = this.orders.map(item => item.id);
                } else {
                    this.selectedItems = [];
                }
                this.updateCount();
            },
            updateCount() {
                this.selectedCount = this.selectedItems.length;
            },
            init() {
                const initialData = JSON.parse(@json($initialData));
                this.orders = initialData.orders;
                this.links = initialData.links;
                // this.fetchData(urls.baseUrl);
                this.orders.forEach(item => {
                    item.showDetails = false;
                });
                // Watch for changes to currentPage and fetch new data accordingly
                this.$watch('currentPage', (newPage) => {
                    this.fetchData(`${urls.baseUrl}?page=${newPage}`);
                });
                console.log(this.orders);
                // console.log(this.links);
                //console.log(this.orderConditions);
            },
            fetchData(baseUrl, params = this.searchParams) {
                const url = new URL(baseUrl);
                console.log(this.perPage);
                url.searchParams.set('perPage', this.perPage);
                // Add search parameters from the current state
                Object.entries(params).forEach(([key, value]) => {
                    if (value) {
                        url.searchParams.set(key, value);
                    }
                });
                console.log(params);
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Mark the request as AJAX
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    console.log(response);
                    return response.json();
                })
                .then(data => {
                    console.log(data.orders);
                    this.orders = data.orders;
                    this.links = data.links;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            },
            submitForm() {
                //console.log(this.searchParams);
                this.fetchData(urls.baseUrl);
            },
            getStatus(statusCode) {
                const numericCode = Number(statusCode); // Chuyển đổi statusCode từ chuỗi sang số
                switch (numericCode) {
                    case 1: return 'Đặt hàng';
                    case 2: return 'Đang XL';
                    case 3: return 'Đang giao';
                    case 4: return 'Đơn hoàn';
                    case 5: return 'Đơn hủy';
                    case 6: return 'Thất lạc';
                    case 7: return 'Hoàn thành';
                    default: return '_';
                }
            },
            getOrderConditionName(id) {
                id = parseInt(id);  // Chuyển đổi id sang số nếu nó là chuỗi
                let condition = this.orderConditions.find(c => c.id === id);
                return condition ? condition.name : '_';
            },
            saveCancelReturn(order, orderId) {
                const saveOrder = {
                    order_id: orderId,
                    type: order.type,
                    reason_id: order.reason_id,
                    carrier_return_date: order.carrier_return_date,
                    processed_by: order.processed_by,
                    notes: order.notes
                };
                console.log(saveOrder);
                const url = "update-order-cancel-return";
                fetch(url, {
                    method: 'POST',  // Sử dụng 'PUT' nếu bạn đang cập nhật thông tin
                    headers: {
                        'Content-Type': 'application/json',
                        // Thêm token nếu API yêu cầu xác thực
                        // 'Authorization': 'Bearer your-token-here',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(saveOrder)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();  // Chuyển kết quả trả về dưới dạng JSON
                })
                .then(data => {
                    console.log('Success:', data);
                    toggleModal(true); // Hiển thị modal khi có thông báo thành công
                        setTimeout(function() {
                            toggleModal(false); // Ẩn modal sau 500ms
                        }, 500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi cập nhật thông tin đơn hàng');
                });
            },
            updateOrder(order) {
                const updatedOrder = {
                    id: order.id,
                    order_id: order.order_id,
                    status_id: order.status_id,
                    order_condition_id: order.order_condition_id,
                    responsible_user_id: order.responsible_user_id,
                    processing_date: order.processing_date,
                    notes: order.notes,
                    result: order.result,
                    ship_date: order.ship_date,
                    estimated_delivery_date: order.estimated_delivery_date,
                    actual_delivery_date: order.actual_delivery_date,
                    approval_time: order.approval_time,
                    packing_time: order.packing_time,
                    delivery_handoff_time: order.delivery_handoff_time,
                    completion_time: order.completion_time,
                    received_return_date: order.received_return_date,
                    cancel_and_return: order.cancel_and_return
                };
                console.log(updatedOrder);
                const url = "update-order-process";
                fetch(url, {
                    method: 'POST',  // Sử dụng 'PUT' nếu bạn đang cập nhật thông tin
                    headers: {
                        'Content-Type': 'application/json',
                        // Thêm token nếu API yêu cầu xác thực
                        // 'Authorization': 'Bearer your-token-here',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(updatedOrder)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();  // Chuyển kết quả trả về dưới dạng JSON
                })
                .then(data => {
                    console.log('Success:', data);
                    toggleModal(true); // Hiển thị modal khi có thông báo thành công
                        setTimeout(function() {
                            toggleModal(false); // Ẩn modal sau 500ms
                        }, 500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi cập nhật thông tin đơn hàng');
                });
            },
            
            formatAmount(amount) {
                return new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 0 }).format(amount);
            },
            formatDate(dateString) {
                const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
                return new Date(dateString).toLocaleString('vi-VN', options);
            }



        }));
    });

</script>
@endpush