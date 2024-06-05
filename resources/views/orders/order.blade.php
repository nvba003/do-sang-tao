@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<x-conditional-content :condition="auth()->user()->hasRole('admin')">
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
                    <!-- Ngày tạo từ -->
                    <div class="w-full sm:w-1/4 md:w-2/12 xl:w-2/24 px-0 mx-2 mb-2 md:mb-0">
                        <label for="searchCreatedAtFrom" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Ngày tạo từ:</label>
                        <input type="date" id="searchCreatedAtFrom" x-model="searchParams.searchCreatedAtFrom" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <!-- Ngày tạo đến -->
                    <div class="w-full sm:w-1/4 md:w-2/12 xl:w-2/24 px-0 mx-2 mb-2 md:mb-0">
                        <label for="searchCreatedAtTo" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Ngày tạo đến:</label>
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
                    <!-- Đóng gói -->
                    <div class="w-full sm:w-1/4 md:w-2/12 xl:w-2/24 px-2 mb-1 md:mb-0">
                        <label for="packingStatus" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Đóng gói:</label>
                        <select id="packingStatus" x-model="searchParams.packingStatus" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            <option value="0">Chưa chuyển</option>
                            <option value="1">Đã chuyển</option>
                        </select>
                    </div>
                    <!-- Thanh toán -->
                    <div class="w-full sm:w-1/4 md:w-2/12 xl:w-2/24 px-2 mb-1 md:mb-0">
                        <label for="paymentStatus" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">T.Toán:</label>
                        <select id="paymentStatus" x-model="searchParams.paymentStatus" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            <option value="1">Chưa TT</option>
                            <option value="2">Chưa đủ</option>
                            <option value="3">Đã TT</option>
                        </select>
                    </div>
                    <!-- Mã vận chuyển -->
                    <div class="w-full sm:w-1/4 md:w-2/12 xl:w-2/24 px-2 mb-1 md:mb-0">
                        <label for="shipping" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Mã VC:</label>
                        <select id="shipping" x-model="searchParams.shipping" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            <option value="0">Chưa có</option>
                            <option value="1">Đã có</option>
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
        @include('orders.partial_order_table', compact(
            'branch_id',
            'products',
            'users',
            'carriers',
            'platforms',
            'orderTypes',
            'orderStatuses',
            'customers',
            'productProcessings',
            'promotions'
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
    // document.addEventListener('DOMContentLoaded', function() {
    //     flatpickr('#searchCreatedAtFrom', {
    //         dateFormat: 'Y-m-d',
    //         defaultDate: '{{ \Carbon\Carbon::now()->subDays(7)->format('Y-m-d') }}'
    //     });
    //     flatpickr('#searchCreatedAtTo', {
    //         dateFormat: 'Y-m-d',
    //         defaultDate: '{{ \Carbon\Carbon::now()->format('Y-m-d') }}'
    //     });
    // });
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
        baseUrl: `{{ url('/order') }}/${branchId}`,
        fetchPackingOrders: "{{ route('packingOrder.fetch') }}"
    };
    const today = new Date();
    const sevenDaysAgo = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 7);

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
                searchCreatedAtFrom: sevenDaysAgo.toISOString().slice(0, 10), // Đặt ngày bắt đầu là 7 ngày trước
                searchCreatedAtTo: today.toISOString().slice(0, 10), // Đặt ngày kết thúc là hôm nay
                status: '',
                packingStatus: '',
                paymentStatus: '',
                platform: '',
            },
            selectedItems: [],
            checkAll: false,
            selectedCount: 0,
            customers: @json($customers),
            products: @json($products),
            productProcessings: @json($productProcessings),//danh sách sản phẩm của đơn hàng có orderProcess là 1,2,3=>xem SP đơn khác
            openModalDiff: false,
            selectedProductDetails: [],//chức năng xem SL sản phẩm ở đơn khác
            packingOrders: [],
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
                this.fetchPackingOrders();
                // Watch for changes to currentPage and fetch new data accordingly
                this.$watch('currentPage', (newPage) => {
                    this.fetchData(`${urls.baseUrl}?page=${newPage}`);
                });
                console.log(this.orders);
                // console.log(branchId);
                // console.log(this.links);
                console.log(this.productProcessings);
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
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Mark the request as AJAX
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
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
            async fetchPackingOrders() {
                try {
                    const response = await fetch(urls.fetchPackingOrders); // Sử dụng URL đúng
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const data = await response.json();
                    //console.log('Packing orders:', data);
                    this.packingOrders = data;
                    return data; // Trả về data để có thể sử dụng ở nơi khác
                } catch (error) {
                    console.error('Error loading packing orders:', error);
                    throw error; // Đẩy lỗi ra ngoài để có thể xử lý ở hàm gọi
                }
            },
            autocompleteSetup(initialCustomerId) {
                return {
                    selectedCustomer: initialCustomerId,
                    selectedCustomerLabel: '',
                    initAutocomplete() {
                        const initialCustomer = this.customers.find(customer => customer.id === initialCustomerId);
                        if (initialCustomer) {
                            this.selectedCustomerLabel = initialCustomer.customer_code;// + ' - ' + initialCustomer.name;
                        }
                        Alpine.nextTick(() => {
                            $(this.$refs.customerInput).autocomplete({
                                source: this.customers.map(customer => ({
                                    label: customer.customer_code + ' - ' + customer.name,
                                    value: customer.id
                                })),
                                select: (event, ui) => {
                                    console.log('Selected:', ui.item);
                                    this.selectedCustomer = ui.item.value;
                                    this.selectedCustomerLabel = ui.item.label;  // Nhãn để hiển thị
                                    return false;
                                }
                            });
                        });
                    }
                }
            },
            autocompleteProductSetup(detail) {
                return {
                    initAutocompleteProduct() {
                        Alpine.nextTick(() => {
                            $(this.$refs.productInput).autocomplete({
                                source: this.products.map(product => ({
                                    label: product.sku + ' - ' + product.name,
                                    order_id: detail.order_id,
                                    sku: product.sku,
                                    name: product.name,
                                    quantity: product.quantity,
                                    price: product.price,
                                    value: product.product_api_id,
                                    bundles: product.bundles || [],
                                    bundle_id: product.bundle_id,
                                    containers: product.containers
                                })),
                                select: (event, ui) => {
                                    console.log('Selected:', ui.item);
                                    // Cập nhật trực tiếp vào đối tượng detail
                                    detail.product_api_id = ui.item.value;
                                    detail.product.product_api_id = ui.item.value;
                                    detail.product.sku = ui.item.sku;
                                    detail.product.name = ui.item.name;
                                    detail.product.quantity = ui.item.quantity;
                                    detail.product.containers = ui.item.containers;
                                    detail.price = ui.item.price;
                                    detail.total = ui.item.price * detail.quantity;
                                    detail.bundles = ui.item.bundles;
                                    detail.bundle_id = ui.item.bundle_id;
                                    // Đẩy lại để cập nhật UI
                                    this.$nextTick(() => this.$el.dispatchEvent(new CustomEvent('input', { bubbles: true })));
                                    return false;
                                }
                            });
                        });
                    },
                }
            },
            calculateMinimumProductQuantity(bundles) {
                let minQuantity = null; // Biến để lưu số lượng nhỏ nhất có thể đạt được
                bundles.forEach(bundle => {
                    let totalContainerQuantity = 0; // Tổng số lượng container cho mỗi bundle
                    bundle.product.containers.forEach(container => {
                        if (container.branch_id === branchId) {
                            totalContainerQuantity += parseFloat(container.product_quantity);
                        }
                    });
                    // Tính số lượng có thể đạt được cho bundle này
                    let achievableQuantity = totalContainerQuantity / parseFloat(bundle.quantity);
                    // Xác định số lượng nhỏ nhất có thể đạt được
                    if (minQuantity === null || achievableQuantity < minQuantity) {
                        minQuantity = achievableQuantity;
                    }
                });
                return minQuantity;
            },
            calculateTotalQtyContainers(detail) {
                // console.log(detail);
                if (detail.product.containers) {
                    const branchId = this.order.branch_id;
                    return detail.product.containers
                        .filter(container => container.branch_id === branchId)
                        .reduce((total, container) => total + parseFloat(container.product_quantity || 0), 0);
                }
            },
            checkQtyContainer(detail) {
                let totalQuantity = detail.bundles.length > 0 ? this.calculateMinimumProductQuantity(detail.bundles) : this.calculateTotalQtyContainers(detail);
                console.log(totalQuantity);
                return totalQuantity < detail.quantity ? 'bg-red-400' : 'bg-green-500';
            },
            calculateTotalQtyDiff(productApiId, currentOrderId) {
                let totalQuantity = 0;
                const details = this.productProcessings[productApiId] || [];
                details.forEach(detail => {
                    if (detail.order_id !== currentOrderId) {
                        totalQuantity += parseFloat(detail.quantity);
                    }
                });
                return totalQuantity;
            },
            openProductDetails(productApiId, currentOrderId) {
                this.selectedProductDetails = this.productProcessings[productApiId] || [];
                this.selectedProductDetails = this.selectedProductDetails.filter(detail => detail.order_id !== currentOrderId);
                // this.openModalDiff = true;
                if (this.selectedProductDetails.length > 0) {
                    this.openModalDiff = true;
                } else {
                    this.openModalDiff = false;
                    console.log(`Không tìm thấy SP có ID ${productApiId} trong đơn hàng có ID ${currentOrderId}`);
                }
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
                    default: return 'Unknown';
                }
            },
            getPaymentStatus(paymentCode) {
                const numericCode = Number(paymentCode); // Chuyển đổi statusCode từ chuỗi sang số
                switch (numericCode) {
                    case 1: return '_';
                    case 2: return 'Chưa đủ';
                    case 3: return '✔️';
                    default: return 'Unknown';
                }
            },
            addDetail() {
                // Tìm ID lớn nhất hiện tại trong danh sách để tránh trùng lặp
                // const maxId = this.order.details.reduce((max, detail) => Math.max(max, detail.id || 0), 0);
                this.order.details.push({
                    // id: maxId + 1,
                    id: Date.now(),// Trả về số milliseconds từ 1/1/1970 đến hiện tại
                    order_id: this.order.id,
                    product_api_id: '',
                    quantity: 0,
                    price: 0,
                    discount_percent: 0,
                    discount: 0,
                    total: 0,
                    promotion_id: null,
                    bundle_id: null,
                    bundles: [],
                    notes: '',
                    is_cancelled: 0,
                    product: {
                        product_api_id: '',
                        sku: '',
                        name: '',
                    }
                });             
            },
            async addPackingDetail() {
                try {
                    await this.fetchPackingOrders(); // Đợi kết quả từ fetchPackingOrders
                    if (this.packingOrders.includes(this.order.id)) {
                        alert('Không cập nhật được. Đơn hàng này đang được đóng gói!');
                        return; // Ngưng thực thi nếu order ID đã tồn tại trong packingOrders
                    }
                    let orderDetails = {
                        order_id: this.order.id,
                        branch_id: this.order.branch_id,
                        platform_id: this.order.platform_id,
                        products: {}
                    };
                    this.order.details.forEach(detail => {
                        let productsToProcess = [];
                        // Kiểm tra xem detail có bundles không, nếu có thì xử lý từng sản phẩm trong bundle
                        if (detail.bundles && detail.bundles.length > 0) {
                            detail.bundles.forEach(bundle => {
                                // Đảm bảo rằng sản phẩm trong bundle được xử lý
                                productsToProcess.push({
                                    sku: bundle.product.sku,
                                    name: bundle.product.name,
                                    bundle_id: detail.bundle_id,
                                    product_api_id: bundle.product.product_api_id,
                                    quantity: detail.quantity * bundle.quantity,//số lượng bằng SL sản phẩm x SL trong bundle
                                    notes: detail.notes || '', // Lấy notes từ detail nếu có
                                    containers: bundle.product.containers || []
                                });
                            });
                        } else {
                            // Nếu không có bundle, xử lý sản phẩm gốc
                            productsToProcess.push({
                                sku: detail.product.sku,
                                name: detail.product.name,
                                bundle_id: '',
                                product_api_id: detail.product.product_api_id,
                                quantity: detail.quantity,
                                notes: detail.notes || '',
                                containers: detail.product.containers || []
                            });
                        }
                        // Xử lý từng sản phẩm được định nghĩa ở trên
                        productsToProcess.forEach(item => {
                            if (!orderDetails.products[item.product_api_id]) {
                                orderDetails.products[item.product_api_id] = {
                                    sku: item.sku,
                                    name: item.name,
                                    bundle_id: item.bundle_id,
                                    quantity: 0,
                                    notes: item.notes,
                                    containers: []
                                };
                            }
                            // Cập nhật thông tin số lượng
                            orderDetails.products[item.product_api_id].quantity += parseFloat(item.quantity);

                            let filteredContainers = item.containers.filter(container =>
                                container.branch_id === this.order.branch_id && parseFloat(container.product_quantity) > 0
                            ).map(container => ({
                                container_id: container.id,
                                container_code: container.container_code,
                                product_quantity: container.product_quantity
                            }));
                            // Thêm containers vào danh sách containers của sản phẩm
                            orderDetails.products[item.product_api_id].containers.push(...filteredContainers);
                        });
                    });
                    console.log(orderDetails);
                    // Giả sử bạn gửi orderDetails này đến server
                    fetch('add-packing-details', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(orderDetails)
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        console.log('Success:', data);
                        this.order.auxpacking_order = data['auxpackingOrder']; //đặt giá trị để cập nhật packingStatus
                        // console.log(data['auxpackingOrder']);
                        toggleModal(true); // Hiển thị modal khi có thông báo thành công
                        setTimeout(function() {
                            toggleModal(false); // Ẩn modal sau 500ms
                        }, 500);
                    })
                    .catch(error => console.error('Error:', error));

                } catch (error) {
                    console.error('Failed to fetch or process packing orders:', error);
                }
            },
            updateOrderTotal(order) {
                let totalDiscount = 0;
                let subtotal = 0;
                order.details.forEach(detail => {
                    if (!detail.is_cancelled) { // Kiểm tra xem sản phẩm có bị hủy không
                        const detailTotal = detail.price * detail.quantity;
                        const detailDiscount = detail.discount_percent > 0
                            ? detailTotal * (detail.discount_percent / 100)
                            : detail.discount;
                        detail.total = detailTotal - detailDiscount;
                        subtotal += detail.total; // Cộng dồn vào tổng phụ
                        // totalDiscount += detailDiscount; // Cộng dồn tổng chiết khấu
                    }
                });
                const totalAmount = subtotal + (order.customer_shipping_fee - order.shipping_fee) - order.total_discount;
                const taxAmount = totalAmount * (order.tax / 100);
                const finalAmount = totalAmount + taxAmount - order.commission_fee;
                // Cập nhật các giá trị tổng cho đơn hàng
                order.subtotal = subtotal;
                order.total_amount = totalAmount;
                order.final_amount = finalAmount;
            },
            updateDetails() {
                console.log(this.order);
                // Kiểm tra trùng lặp product_api_id
                let productApiIds = new Set();
                for (const detail of this.order.details) {
                    if (productApiIds.has(detail.product_api_id)) {
                        alert('Lỗi: Có sản phẩm bị trùng lặp.');
                        return; // Ngưng thực thi nếu tìm thấy trùng lặp
                    }
                    productApiIds.add(detail.product_api_id);
                }
                const url = 'update-order';
                fetch(url, {
                    method: 'POST', // Hoặc 'PUT' nếu bạn đang cập nhật
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.order)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Success:', data);
                    toggleModal(true); // Hiển thị modal khi có thông báo thành công
                    setTimeout(function() {
                        toggleModal(false); // Ẩn modal sau 500ms
                    }, 500);
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('Lỗi không lưu được');
                });
            },
            deleteDetail(detail) {
                const index = this.order.details.indexOf(detail);
                if (index !== -1) {
                    this.order.details.splice(index, 1);
                }
                this.updateOrderTotal(this.order);//tính lại số tổng
            },
            updateInfo(order) {
                const updatedInfoOrder = {
                    id: order.id,  // Giả sử bạn cần ID để xác định đơn hàng cần cập nhật
                    carrier_id: order.order_process.carrier_id,
                    responsible_user_id: order.order_process.responsible_user_id,
                    customer_account_id: order.customer_account_id,
                    customer_id: order.customer_account.customer_id,
                    tracking_number: order.order_process.tracking_number,
                    order_type_id: order.order_type_id,
                    source_info: order.source_info,
                    status_id: order.order_process.status_id,
                    notes: order.notes
                };
                console.log(updatedInfoOrder);
                const url = "update-info-order";
                fetch(url, {
                    method: 'POST',  // Sử dụng 'PUT' nếu bạn đang cập nhật thông tin
                    headers: {
                        'Content-Type': 'application/json',
                        // Thêm token nếu API yêu cầu xác thực
                        // 'Authorization': 'Bearer your-token-here',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(updatedInfoOrder)
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
            addFinance(financeAmount) {
                let newFinance = {
                        id: '',
                        order_id: '',
                        amount_due: '',
                        amount_paid: '',
                        amount_remaining: '',
                        created_at: '',
                    };
                if (this.order.finances.length === 0) {//nếu chưa có thanh toán
                    newFinance = {
                        id: Date.now(),
                        order_id: this.order.id,
                        amount_due: this.order.total_amount,
                        amount_paid: financeAmount,
                        amount_remaining: this.order.total_amount - financeAmount,
                        created_at: Date.now(),
                    };
                } else {
                    const lastFinance = this.order.finances[this.order.finances.length - 1];// Lấy thông tin tài chính cuối cùng và cập nhật
                    if (financeAmount === this.order.total_amount) {//nếu chọn thanh toán đủ
                        financeAmount = lastFinance.amount_remaining;//số tiền thanh toán là số tiền còn lại
                    }
                    newFinance = {
                        id: Date.now(),
                        order_id: this.order.id,
                        amount_due: lastFinance.amount_remaining,
                        amount_paid: financeAmount,
                        amount_remaining: lastFinance.amount_remaining - financeAmount,
                        created_at: Date.now(),
                    };
                }
                console.log(newFinance);
                const url = "add-finance-order";
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(newFinance)
                }).then(response => response.json())
                .then(data => {
                    console.log('Success:', data);
                    this.order.finances.push(newFinance);
                    this.order.payment = data['payment'];//cập nhật giá trị trạng thái thanh toán
                    this.financeAmount = '';
                    toggleModal(true); // Hiển thị modal khi có thông báo thành công
                        setTimeout(function() {
                            toggleModal(false); // Ẩn modal sau 500ms
                        }, 500);
                })
                .catch(error => {
                    alert('Lỗi không thêm được');
                    console.error('Error:', error);
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


    // $(document).ready(function() {
    //     let currentSearchParams = "";
    //     let currentPerPage = "";
    //     let perPage = $('#perPage').val();
    //     // Truyền giá trị branch_id từ Blade vào JavaScript
    //     const branchId = {{ $branch_id }};
    //     $.ajaxSetup({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         }
    //     });
    //     function fetchData(url) {
    //         $.ajax({
    //             url: url,
    //             type: 'GET',
    //             success: function(response) {
    //                 $('#orderTable').html(response.table);
    //                 $('#pagination-links').html(response.links);
    //             },
    //             error: function(xhr, status, error) {
    //                 console.error(error);
    //             }
    //         });
    //     }

    //     const baseUrl = `{{ url('/order') }}/${branchId}`;
    //     fetchData(baseUrl);

    //     $('#searchOrder').on('submit', function(e) {
    //         e.preventDefault();
    //         perPage = $('#perPage').val();
    //         currentSearchParams = updateSearchParams('per_page', perPage, $(this).serialize());
    //         fetchData(`${baseUrl}?${currentSearchParams}`);
    //     });
    //     //$('#searchOrder').submit();//tự động submit form khi tải trang ban đầu

    //     $('#pagination-links').on('click', 'a.relative', function(e) {
    //         e.preventDefault();
    //         var href = $(this).attr('href');
    //         fetchData(href + '&' + currentSearchParams); // Thêm tham số tìm kiếm vào URL phân trang
    //     });

    //     $('#perPage').on('change', function() {
    //         perPage = $(this).val();
    //         currentSearchParams = updateSearchParams('per_page', perPage, currentSearchParams);
    //         fetchData(`${baseUrl}?${currentSearchParams}`);
    //     });
    //     function updateSearchParams(key, value, paramsString) {
    //         var searchParams = new URLSearchParams(paramsString);
    //         searchParams.set(key, value);
    //         return searchParams.toString();
    //     }

    //     $('#orderTable').on('click', '#checkAll', function() {
    //     // $('#checkAll').on('click', function() {
    //         var isChecked = $(this).prop('checked');
    //         $('.checkItem').prop('checked', isChecked);
    //         updateCount();
    //     });

    //     function updateCount() {
    //         var count = $('.checkItem:checked').length;
    //         $('#selectedCount').text(count);
    //     }
    //     $(document).on('click', '.checkItem', function() {
    //         updateCount();
    //     });
    //     updateCount();  
    // });


</script>
@endpush