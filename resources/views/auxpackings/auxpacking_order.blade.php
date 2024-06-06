@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<x-conditional-content :condition="auth()->user()->hasRole('admin') || auth()->user()->hasRole('packing')">
<div class="container mx-auto px-2 sm:px-3 lg:px-4">
    <div id="orderTable" class="w-full" x-data="orderTable()">
        <div class="flex flex-wrap mx-auto mt-2 p-4 bg-white rounded shadow-md mb-2">
            <form id="searchOrder" @submit.prevent="submitForm" class="w-full mb-1">
                <div class="flex flex-wrap -mx-2">
                    <!-- Tìm đơn hàng -->
                    <div class="w-full sm:w-3/12 xl:w-6/24 px-2 mb-2 md:mb-0">
                        <label for="searchOrderCode" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Tìm đơn hàng:</label>
                        <div class="relative">
                            <input type="text" id="searchOrderCode" x-model="searchParams.searchOrderCode" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập mã đơn hàng">
                            <div class="absolute inset-y-0 right-0 flex items-center px-1">
                                <button type="button" @click="searchParams.searchOrderCode = ''" class="bg-gray-200 hover:bg-gray-300 text-gray-500 text-sm p-2 rounded-r-md">Xóa</button>
                            </div>
                        </div>
                    </div>
                    <!-- Tìm khách hàng -->
                    <div class="w-full sm:w-3/12 xl:w-6/24 px-2 mb-2 md:mb-0">
                        <label for="searchCustomer" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Tìm khách hàng:</label>
                        <div class="relative">
                            <input type="text" id="searchCustomer" x-model="searchParams.searchCustomer" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập tên khách hàng">
                            <div class="absolute inset-y-0 right-0 flex items-center px-1">
                                <button type="button" @click="searchParams.searchCustomer = ''" class="bg-gray-200 hover:bg-gray-300 text-gray-500 text-sm p-2 rounded-r-md">Xóa</button>
                            </div>
                        </div>
                    </div>
                    <!-- Trạng thái -->
                    <div class="w-full sm:w-2/12 xl:w-4/24 px-2 mb-1 md:mb-0">
                        <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Trạng thái:</label>
                        <select id="status" x-model="searchParams.status" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            <option value="1">Chưa quét</option>
                            <option value="2">Đã quét</option>
                            <option value="3">Đã giao</option>
                        </select>
                    </div>
                    <!-- Kênh bán hàng -->
                    <div class="w-full sm:w-2/12 xl:w-4/24 px-2 mb-1 md:mb-0">
                        <label for="platform" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Kênh:</label>
                        <select id="platform" x-model="searchParams.platform" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            @foreach($platforms as $platform)
                                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Nút Tìm -->
                    <div class="w-full sm:w-2/12 xl:w-2/24 px-2 py-4 flex items-end">
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 w-full">Tìm</button>
                    </div>
                </div>
            </form>
        </div>
        @include('auxpackings.partial_auxpacking_order_table', compact(
            'branch_id',
            'orders',
            'branches',
            'users',
            'carriers',
            'platforms',
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
        baseUrl: `{{ url('/auxpacking-order') }}/${branchId}`
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
                status: '',
                platform: '',
            },
            selectedItems: [],
            checkAll: false,
            selectedCount: 0,
            allContainers: @json($allContainers),
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
                    item.products.forEach(product => {
                        product.isSelected = false;
                        product.containers.forEach(container => {
                            container.isSelected = container.status === 1; // Thiết lập isSelected dựa trên status
                        });
                    });
                });
                // Watch for changes to currentPage and fetch new data accordingly
                this.$watch('currentPage', (newPage) => {
                    this.fetchData(`${urls.baseUrl}?page=${newPage}`);
                });
                console.log(this.orders);
                console.log(this.allContainers);
                // console.log(this.links);
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
                    case 1: return '_';
                    case 2: return 'Đã quét';
                    case 3: return 'Đã giao';
                    default: return '_';
                }
            },
            getProductStatus(statusCode) {
                const numericCode = Number(statusCode); // Chuyển đổi statusCode từ chuỗi sang số
                switch (numericCode) {
                    case 1: return 'Chưa lấy';
                    case 2: return 'Lấy chưa đủ';
                    case 3: return 'Lấy đủ';
                    default: return '_';
                }
            },
            getBackgroundColor(status) {
                switch(status) {
                    case 1: return 'bg-red-300';
                    case 2: return 'bg-yellow-300';
                    case 3: return 'bg-green-500';
                    default: return 'bg-white';
                }
            },
            
            formatAmount(amount) {
                return new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 0 }).format(amount);
            },
            formatDate(dateString) {
                const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
                return new Date(dateString).toLocaleString('vi-VN', options);
            },

            containerManager() {
                return {
                    containers: [],
                    //newContainer: {},
                    newContainer: { container: [], quantity: '', notes: '' },
                    showAddContainerForm: false,
                    branchId: null,
                    platformId: null,
                    orderId: null,
                    auxpackingProductId: null,
                    productApiId: null,
                    containerId: null,
                    openAddFormWithCopy: this.openAddFormWithCopy,
                    addNewContainer: this.addNewContainer,
                    
                    init() {
                        // Khởi tạo containers nếu có
                    },
                    selectContainer(container) {
                        container.isSelected = !container.isSelected; // Toggle selection
                        this.saveSelectContainer(container);
                    },
                    saveSelectContainer(container) {
                        console.log(container);
                        fetch('{{ route('auxPackingContainer.update') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                containerId: container.id, // cập nhật trên bảng auxpacking_container
                                isSelected: container.isSelected ? 1 : 0, // Chuyển đổi boolean thành số nguyên
                                quantity: container.quantity
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Server responded:', data);
                            let product = this.order.products.find(p => p.product_api_id === data.auxpackingProduct.product_api_id);
                            product.status = data.auxpackingProduct.status;//cập nhật trạng thái
                        })
                        .catch(error => {
                            console.error('Error posting data:', error);
                            alert('Lỗi! Chưa lưu được.');
                        });
                    },

                    openAddFormWithCopy(container) {
                        //console.log(container);
                        // Tạo một bản sao của container hiện tại để chỉnh sửa, không làm thay đổi dữ liệu ban đầu
                        this.newContainer = JSON.parse(JSON.stringify(container));
                        this.newContainer.container = null;//xóa container copy
                        this.newContainer.isSelected = false;
                        this.newContainer.notes = null;
                        this.showAddContainerForm = true;
                    },
                    addNewContainer() {
                        this.newContainer.id = Date.now();//đặt Id tạm
                        const addedContainer = this.newContainer;
                        
                        this.newContainer = {};// Xóa dữ liệu form tạm
                        let data = {
                            branch_id: addedContainer.branch_id,
                            platform_id: addedContainer.platform_id,
                            order_id: addedContainer.order_id,
                            auxpacking_product_id: addedContainer.auxpacking_product_id,
                            product_api_id: addedContainer.product_api_id,
                            container_id: addedContainer.container.id,
                            quantity: addedContainer.quantity,
                            notes: addedContainer.notes
                        };
                        console.log(data);
                        fetch('{{ route('auxPackingContainer.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(data)
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Success:', data);
                            addedContainer.id = data.data.id; //gán mới Id vừa tạo
                            let product = this.order.products.find(p => p.product_api_id === addedContainer.product_api_id);
                            product.containers.push(addedContainer);//thêm UI
                            this.showAddContainerForm = false;
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            alert('Lỗi! Chưa tạo mới được.');
                        });
                    },
                    removeContainer(container) {
                        const containerId = container.id;
                        const containerSelect = container.isSelected;
                        console.log(container);
                        if (containerSelect === true) {
                            alert('Thùng đã lấy, không thể xóa.');
                            return;
                        }
                        if (!confirm('Bạn có chắc chắn muốn xóa container này không?')) {
                            return;
                        }
                        fetch('{{ route('auxPackingContainer.remove') }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ containerId: containerId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Success:', data);
                            // Tìm sản phẩm chứa container này
                            let product = this.order.products.find(p => p.containers.some(c => c.id === containerId));
                            if (product) {
                                // Tìm chỉ số của container trong mảng containers
                                let containerIndex = product.containers.findIndex(c => c.id === containerId);
                                // Kiểm tra xem container có tồn tại trong mảng không
                                if (containerIndex > -1) {
                                    // Xóa container khỏi mảng
                                    product.containers.splice(containerIndex, 1);
                                }
                            }
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            alert('Lỗi! Chưa xóa được.');
                        });
                    }

                }
            },//end containerManager

        }));
    });

</script>
@endpush