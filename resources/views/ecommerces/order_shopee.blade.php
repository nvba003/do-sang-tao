@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
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


    <div class="bg-white shadow-md rounded-lg my-1 overflow-x-auto p-2 sm:p-4">
        
        <table id="orderTable" class="w-full bg-white border border-gray-200 rounded-lg" x-data="orderTable()">
            <thead class="text-white bg-gray-500">
                <tr>
                    <th scope="col" class="w-1/24 px-2 py-3 mt-1 text-center text-xs md:text-sm hidden sm:block font-semibold uppercase tracking-wider">
                        <input type="checkbox" id="checkAll">
                    </th>
                    <th scope="col" class="w-1/24 px-2 py-3 text-left text-center text-xs md:text-sm font-semibold uppercase tracking-wider"></th>
                    <th scope="col" class="w-5/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Mã đơn</th>
                    <th scope="col" class="w-5/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Khách hàng</th>
                    <!-- <th scope="col" class="w-2/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Phone</th>
                    <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Địa chỉ</th> -->
                    <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Tổng tiền</th>
                    <th scope="col" class="w-4/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">NVC</th>
                    <th scope="col" class="w-4/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Mã VC</th>
                    <!-- <th scope="col" class="w-6/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Ngày đặt</th> -->
                    <th scope="col" class="w-1/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Check</th>
                </tr>
            </thead>
            <tbody>
                @include('ecommerces.partial_order_shopee_table', ['orders' => $orders, 'users' => $users, 'carriers' => $carriers])
            </tbody>
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

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr('#searchCreatedAtFrom', {
            dateFormat: 'Y-m-d',
            defaultDate: '{{ \Carbon\Carbon::now()->subDays(7)->format('Y-m-d') }}'
        });
        flatpickr('#searchCreatedAtTo', {
            dateFormat: 'Y-m-d',
            defaultDate: '{{ \Carbon\Carbon::now()->format('Y-m-d') }}'
        });
    });

    function clearSearchOrder() {
        document.getElementById('searchOrderCode').value = '';
    }
    function clearSearchCustomer() {
        document.getElementById('searchCustomer').value = '';
    }
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
    document.addEventListener('alpine:init', () => {
        Alpine.data('orderTable', () => ({
            // openDetails: null,
            // toggleDetails(orderId, event) {
            //     this.openDetails = this.openDetails === orderId ? null : orderId;
            //     event.target.textContent = event.target.textContent === '-' ? '+' : '-';
            // },
            openDetails: [],
            toggleDetails(orderId) {
                const index = this.openDetails.indexOf(orderId);
                if (index > -1) {
                    this.openDetails.splice(index, 1);
                } else {
                    this.openDetails.push(orderId);
                }
            },
            sendOrder(order) {
                const orderId = order.id;
                const orderRow = document.querySelector(`#details${orderId}`);
                const carrierId = orderRow.querySelector(`#carrier_${orderId}`).value;
                const trackingNumber = orderRow.querySelector(`#tracking_${orderId}`).value;
                const notes = orderRow.querySelector(`#notes_${orderId}`).value;
                const responsibleUserId = orderRow.querySelector(`#responsible_${orderId}`).value;
                const platformId = orderRow.querySelector(`#platform_${orderId}`).value;
                const updatedOrderId = document.querySelector(`#order-id-${orderId} span`).textContent.trim();// Lấy order_id từ thẻ span đã cập nhật

                const productDetails = [];
                orderRow.querySelectorAll('.autocomplete-product').forEach(input => {
                    const detailId = input.dataset.detailId;
                    const productApiId = input.getAttribute('data-product-id');//product id trong table details => người dùng có thể thay đổi
                    const initialProductApiId = input.dataset.initialProductId;//ban đầu product id trong mối quan hệ product
                    
                    const serialElement = input.closest('tr').querySelector('td[data-serial]');
                    const serial = serialElement ? serialElement.dataset.serial : null;
                    // Lấy quantity từ cột tương ứng
                    const quantityElement = input.closest('tr').querySelector('td[data-quantity]');
                    const quantity = quantityElement ? quantityElement.dataset.quantity : null;
                    // console.log(initialProductApiId);
                    // console.log(productApiId);
                    // Check if the product API ID has changed: //nếu khác nhau có nghĩa là do người dùng thay đổi => lấy thông tin được thay đổi => không thì giữ nguyên
                    productDetails.push({
                        serial: serial,
                        detail_ecom_id: detailId,
                        product_api_id: productApiId !== initialProductApiId ? productApiId : initialProductApiId,
                        product_api_id_before: initialProductApiId,//lấy data để xem có cần ghi vào sendo_details không
                        quantity: quantity,
                    });
                });

                const orderData = {
                    order_ecom: order,
                    order_id: updatedOrderId,
                    carrier_id: carrierId,
                    tracking_number: trackingNumber,
                    notes: notes,
                    responsible_user_id: responsibleUserId,
                    platform_id: platformId,
                    product_details: productDetails,
                };
                console.log(orderData);
                fetch('{{ route('orderShopee.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify(orderData)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    const orderIdCheck = document.getElementById('order-id-' + orderId);//orderId là id trong order_sendos
                    const orderIdSpan = orderIdCheck.querySelector('span');
                    orderIdSpan.textContent = data.order_id;//data.order_id là id trong orders được trả về từ server
                    const orderRow = orderIdCheck.closest('tr');
                    orderRow.classList.remove('bg-white'); // Loại bỏ lớp nền cũ nếu có
                    orderRow.classList.add('bg-green-500');  // Thêm lớp nền mới
                    toggleModal(true); // Hiển thị modal khi có thông báo thành công
                    setTimeout(function() {
                        toggleModal(false); // Ẩn modal sau 500ms
                    }, 500);
                    console.log('Success:', data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }));
    });

    $(document).ready(function() {
        let currentSearchParams = "";
        let currentPerPage = "";
        let perPage = $('#perPage').val();
        // Truyền giá trị platform_id từ Blade vào JavaScript
        const platformId = {{ $platform_id }};
        //console.log(platformId);
        var orders = @json($orders)['data'];
        console.log(orders);
        var products = @json($products);
        var carriers = @json($carriers);
        console.log(carriers);

        $('#orderTable').on('focus', '.autocomplete-product', function() {
            $(this).autocomplete({
                source: products.map(product => ({
                    label: product.sku + " - " + product.name,
                    value: product.id
                })),
                select: function(event, ui) {
                    $(this).val(ui.item.label);
                    $(this).attr('data-product-id', ui.item.value); // Use attr method to update data-product-id attribute
                    return false;
                }
            });
        });

        function fetchData(url) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#orderTable tbody').html(response.table);
                    $('#pagination-links').html(response.links);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        const baseUrl = `{{ url('/order-shopee') }}/${platformId}`;
        fetchData(baseUrl);

        $('#searchOrder').on('submit', function(e) {
            e.preventDefault();
            perPage = $('#perPage').val();
            currentSearchParams = updateSearchParams('per_page', perPage, $(this).serialize());
            fetchData(`${baseUrl}?${currentSearchParams}`);
        });
        $('#searchOrder').submit();//tự động submit form khi tải trang ban đầu

        $('#pagination-links').on('click', 'a.relative', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            fetchData(href + '&' + currentSearchParams); // Thêm tham số tìm kiếm vào URL phân trang
        });

        $('#checkAll').on('click', function() {
            var isChecked = $(this).prop('checked');
            $('.checkItem').prop('checked', isChecked);
            updateCount();
        });

        $('#perPage').on('change', function() {
            perPage = $(this).val();
            currentSearchParams = updateSearchParams('per_page', perPage, currentSearchParams);
            fetchData(`${baseUrl}?${currentSearchParams}`);
        });
        function updateSearchParams(key, value, paramsString) {
            var searchParams = new URLSearchParams(paramsString);
            searchParams.set(key, value);
            return searchParams.toString();
        }

        function updateCount() {
            var count = $('.checkItem:checked').length;
            $('#selectedCount').text(count);
        }
        $(document).on('click', '.checkItem', function() {
            updateCount();
        });
        updateCount();  

        // $('#orderTable').on('click', '.btn-edit', function() {
        //     var promotion = $(this).data('promotion');
        //     openEditForm(promotion);
        // });
        

    });


</script>
@endpush