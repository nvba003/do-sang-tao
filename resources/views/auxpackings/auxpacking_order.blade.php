@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<x-conditional-content :condition="auth()->user()->hasRole('admin')">
<div class="container mx-auto px-2 sm:px-3 lg:px-4">
    <div class="flex flex-wrap mx-auto mt-2 p-4 bg-white rounded shadow-md mb-2">
        <form id="searchOrder" method="GET" class="w-full mb-1">
            <div class="flex flex-wrap -mx-2">
                <!-- Tìm đơn hàng -->
                <div class="w-full sm:w-1/3 md:w-3/12 xl:w-2/12 px-2 mb-1 md:mb-0">
                    <label for="searchOrderCode" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Tìm đơn hàng:</label>
                    <div class="relative">
                        <input type="text" id="searchOrderCode" name="searchOrderCode" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập mã đơn hàng">
                        <div class="absolute inset-y-0 right-0 flex items-center px-2">
                            <button class="bg-gray-200 hover:bg-gray-300 text-gray-500 p-2 rounded-r-md" type="button" onclick="clearSearchOrder()">Xóa</button>
                        </div>
                    </div>
                </div>
                <!-- Tìm khách hàng -->
                <div class="w-full sm:w-1/3 md:w-3/12 xl:w-2/12 px-2 mb-1 md:mb-0">
                    <label for="searchCustomer" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Tìm khách hàng:</label>
                    <div class="relative">
                        <input type="text" id="searchCustomer" name="searchCustomer" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập tên khách hàng">
                        <div class="absolute inset-y-0 right-0 flex items-center px-2">
                            <button class="bg-gray-200 hover:bg-gray-300 text-gray-500 p-2 rounded-r-md" type="button" onclick="clearSearchCustomer()">Xóa</button>
                        </div>
                    </div>
                </div>
                <!-- Ngày tạo từ -->
                <div class="w-full sm:w-1/3 md:w-3/12 xl:w-2/12 px-2 mb-1 md:mb-0">
                    <label for="searchCreatedAtFrom" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Ngày tạo từ:</label>
                    <input type="text" id="searchCreatedAtFrom" name="searchCreatedAtFrom" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Chọn ngày">
                </div>
                <!-- Ngày tạo đến -->
                <div class="w-full sm:w-1/3 md:w-3/12 xl:w-2/12 px-2 mb-1 md:mb-0">
                    <label for="searchCreatedAtTo" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Ngày tạo đến:</label>
                    <input type="text" id="searchCreatedAtTo" name="searchCreatedAtTo" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Chọn ngày">
                </div>
                <!-- Xử lý đơn -->
                <div class="w-full sm:w-1/3 md:w-3/12 xl:w-1/12 px-2 mb-1 md:mb-0">
                    <label for="order_id_check" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Xử lý đơn:</label>
                    <select class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="order_id_check" name="order_id_check">
                        <option value="">Chọn</option>
                        <option value="0">Chưa tạo đơn</option>
                        <option value="1">Đã tạo đơn</option>
                    </select>
                </div>
                <!-- Mã vận chuyển -->
                <div class="w-full sm:w-1/3 md:w-3/12 xl:w-1/12 px-2 mb-1 md:mb-0">
                    <label for="shipping" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Mã VC:</label>
                    <select class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="shipping" name="shipping">
                        <option value="">Chọn</option>
                        <option value="0">Chưa có</option>
                        <option value="1">Đã có</option>
                    </select>
                </div>
                <!-- Trạng thái -->
                <div class="w-full sm:w-1/3 md:w-3/12 xl:w-1/12 px-2 mb-1 md:mb-0">
                    <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Trạng thái:</label>
                    <select class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="status" name="status">
                        <option value="">Chọn</option>
                        <option value="0" selected>Đang XL</option>
                        <option value="1">Đơn hủy</option>
                    </select>
                </div>
                <!-- Nút Tìm -->
                <div class="w-full sm:w-1/3 md:w-3/12 xl:w-1/12 mt-2 px-2 flex items-end">
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 w-full">Tìm</button>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg my-1 overflow-x-auto p-2 sm:p-4" x-data="orderTable()">
    <button @click="updateOrders" class="bg-green-500 text-white px-4 py-2 rounded mb-4">Update orders</button>
        <table id="orderTable" class="w-full bg-white border border-gray-200 rounded-lg">
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
                @include('auxpackings.partial_auxpacking_order_table', ['orders' => $orders, 'users' => $users, 'carriers' => $carriers])
        </table>

</div>
<div class="mx-auto mt-2 max-w-full">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 m-2">
            <div class="lg:col-span-3 md:col-span-1"></div>
            <div class="lg:col-span-7 col-span-1 md:col-span-9" id="pagination-links">
                <!-- Pagination links here -->
            </div>
            <div class="lg:col-span-2 col-span-1 md:col-span-2 justify-end">
                <div class="flex items-center space-x-2">
                    <label for="perPage" class="text-sm flex-grow text-right pr-2">Số hàng:</label>
                    <select id="perPage" class="px-1 py-2 text-sm w-20">
                        <option value="15">15</option>
                        <option value="100">100</option>
                    </select>
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function orderTable() {
        return {
            orders: [], // Dữ liệu sản phẩm sẽ được tải vào đây
            init() {
                this.orders = @json($orders)['data'];
                console.log(this.orders);
                this.orders.forEach(item => {
                    item.showDetails = false;
                    item.products.forEach(product => {
                        product.isSelected = false;
                        product.containers.forEach(container => {
                            container.isSelected = container.status === 1; // Thiết lập isSelected dựa trên status
                        });
                    });
                });
                console.log(this.orders);
            },
            getStatus(statusCode) {
                switch (statusCode) {
                    case 1: return 'Mặc định';
                    case 2: return 'Lấy chưa đủ';
                    case 3: return 'Lấy đủ';
                    case 4: return 'Thiếu sp trong thùng';
                    default: return 'Unknown';
                }
            },
            selectContainer(container) {
                container.isSelected = !container.isSelected; // Toggle selection
                this.sendContainerData(container);
            },
            sendContainerData(container) {
                $.ajax({
                    url: '{{ route('auxPackingContainer.update') }}',
                    type: 'POST',
                    data: {
                        containerId: container.id, //cập nhật trên bảng auxpacking_container
                        isSelected: container.isSelected ? 1 : 0,  // Chuyển đổi boolean thành số nguyên
                    },
                    success: function(response) {
                        console.log('Server responded:', response);
                    },
                    error: function(error) {
                        console.error('Error posting data:', error);
                    }
                });
            },
            updateQuantity(container) {
                // Gửi yêu cầu cập nhật số lượng lên server
                console.log('Updating quantity for container:', container);
                // Giả sử bạn gửi yêu cầu AJAX ở đây
            },

            // updateorders() {
            //     // Cập nhật dữ liệu sản phẩm mới
            //     this.orders = [
            //         { id: 4, name: 'order 4', showDetails: false, containers: ['Container 4A', 'Container 4B'] },
            //         { id: 5, name: 'order 5', showDetails: false, containers: ['Container 5A'] },
            //         { id: 6, name: 'order 6', showDetails: false, containers: [] }
            //     ];
            // }
        }
    }

</script>
@endpush