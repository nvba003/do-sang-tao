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

    <div class="bg-white shadow-md rounded-lg my-1 overflow-x-auto p-2 sm:p-4" x-data="productTable()">
    <button @click="updateProducts" class="bg-green-500 text-white px-4 py-2 rounded mb-4">Update Products</button>
        <table id="productTable" class="w-full bg-white border border-gray-200 rounded-lg">
            <thead class="text-white bg-gray-500">
                <tr>
                    <th scope="col" class="w-1/24 px-2 py-3 mt-1 text-center text-xs md:text-sm hidden sm:block font-semibold uppercase tracking-wider">
                        <input type="checkbox" id="checkAll">
                    </th>
                    <th scope="col" class="w-1/24 px-2 py-3 text-left text-center text-xs md:text-sm font-semibold uppercase tracking-wider"></th>
                    <th scope="col" class="w-4/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Mã SP</th>
                    <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Tên SP</th>
                    <!-- <th scope="col" class="w-2/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Phone</th>
                    <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Địa chỉ</th> -->
                    <th scope="col" class="w-2/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Số lượng</th>
                    <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Đã lấy</th>
                    <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Tổng tiền</th>
                    <!-- <th scope="col" class="w-4/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Ngày đặt</th>
                    <th scope="col" class="w-2/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Check</th> -->
                </tr>
            </thead>
                @include('auxpackings.partial_auxpacking_product_table', ['products' => $products, 'users' => $users])
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
    console.log(@json($productGroups));
    function productTable() {
        return {
            // showAddContainerForm: false,
            // containers: [],
            // newContainer: {},
            allContainers: @json($allContainers),
            products: @json($products),
            init() {
                Object.keys(this.products).forEach(productId => {
                    Object.values(this.products[productId]).forEach(containerGroup => {
                        containerGroup.forEach(container => {
                            container.isSelected = container.status === 1;
                        });
                    });
                    this.products[productId].showDetails = false;// Thêm showDetails cho mỗi nhóm sản phẩm
                });
                console.log(this.products);
                console.log(this.allContainers);
            },
            sumSelectedQuantities(group) {// Phương thức tính tổng số lượng container đã chọn
                return group.reduce((total, container) => {
                    return total + (container.isSelected ? parseFloat(container.quantity) : 0);
                }, 0);
            },
            totalGroupQuantity(group) {
                return group.reduce((total, container) => total + parseFloat(container.quantity), 0);
            },
            selectAllContainers(group) {
                group.forEach(container => {
                    if (!container.isSelected) { // Kiểm tra nếu container chưa được chọn
                        container.isSelected = true; // Đặt trạng thái là được chọn
                        this.sendContainerData(container); // Gửi dữ liệu đến server
                    }
                });
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

            updateProducts(data) {
                this.products = data; // Giả sử 'data' là dữ liệu sản phẩm mới nhận được
                this.init(); // Gọi lại hàm init nếu cần thiết để reset các settings ban đầu
            }

            // containerManager() {
            //     return {
            //         showAddContainerForm: false,
            //         containers: [],
            //         newContainer: {},
                    
            //         init() {
            //             // Khởi tạo containers nếu có
            //         },

            //         openAddFormWithCopy(container) {
            //             this.newContainer = JSON.parse(JSON.stringify(container));
            //             this.newContainer.id = null; // Xóa ID để không ghi đè lên container hiện có
            //             this.newContainer.created_at = null;
            //             this.newContainer.updated_at = null;
            //             this.showAddContainerForm = true;
            //         },

            //         addNewContainer() {
            //             // Thêm newContainer vào danh sách containers sau khi chỉnh sửa
            //             this.containers.push(this.newContainer);
            //             this.showAddContainerForm = false;
            //             this.newContainer = {}; // Reset newContainer
            //         }
            //     }
            // }
            // openAddFormWithCopy(container) {
            //     this.newContainer = JSON.parse(JSON.stringify(container));
            //     this.newContainer.id = null; // Xóa ID để không ghi đè lên container hiện có
            //     this.newContainer.created_at = null;
            //     this.newContainer.updated_at = null;
            //     this.showAddContainerForm = true;
            // },

            // addNewContainer() {
            //     // Thêm newContainer vào danh sách containers sau khi chỉnh sửa
            //     this.containers.push(this.newContainer);
            //     this.showAddContainerForm = false;
            //     this.newContainer = {}; // Reset newContainer
            // }



        }
    }

    function containerManager() {
        return {
            containers: [],
            //newContainer: {},
            newContainer: { selectedContainerId: '', quantity: '', notes: '' },
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

            openAddFormWithCopy(container) {
                // Tạo một bản sao của container hiện tại để chỉnh sửa, không làm thay đổi dữ liệu ban đầu
                this.newContainer = JSON.parse(JSON.stringify(container));
                this.newContainer.selectedContainerId = this.newContainer.selectedContainerId || '';
                // Xóa các trường không cần thiết khi tạo container mới
                //this.newContainer.id = null; // Xóa ID để không ghi đè lên container hiện có
                this.newContainer.created_at = null;
                this.newContainer.updated_at = null;
                // Mở form để thêm mới container
                this.showAddContainerForm = true;
            },

            // addNewContainer() {
            //     // Đảm bảo rằng newContainer có tất cả các trường cần thiết
            //     this.newContainer.branch_id = this.newContainer.branch_id || this.branchId; // Đặt branch_id nếu không có
            //     this.newContainer.platform_id = this.newContainer.platform_id || this.platformId;
            //     this.newContainer.order_id = this.newContainer.order_id || this.orderId;
            //     this.newContainer.auxpacking_product_id = this.newContainer.auxpacking_product_id || this.auxpackingProductId;
            //     this.newContainer.product_api_id = this.newContainer.product_api_id || this.productApiId;
            //     this.newContainer.container_id = this.newContainer.container_id || this.containerId;

            //     // Thêm newContainer vào danh sách containers sau khi chỉnh sửa
            //     this.containers.push(this.newContainer);
            //     console.log('New container added:', this.newContainer); // Kiểm tra xem container mới được thêm như thế nào
            //     console.log('Updated containers list:', this.containers); // Kiểm tra danh sách containers sau khi thêm
            //     this.showAddContainerForm = false;
            //     this.newContainer = {}; // Reset newContainer để sẵn sàng cho lần thêm mới tiếp theo
            // },
            addNewContainer() {
                console.log(this.newContainer);
                // Nhận container mới với ID từ phản hồi
                this.newContainer.id = 20;
                
                const addedContainer = this.newContainer;
                // Tìm nhóm tương ứng hoặc tạo nhóm mới
                let group = this.products[addedContainer.product_api_id];
                if (!group) {
                    this.products[addedContainer.product_api_id] = {1: []}; // Tạo nhóm mới nếu chưa tồn tại
                    group = this.products[addedContainer.product_api_id];
                }
                // Thêm container vào nhóm
                group[1].push(addedContainer);

                // this.containerGroup[1].push(this.newContainer);// Thêm vào danh sách container
                this.showAddContainerForm = false;
                this.newContainer = {};// Xóa dữ liệu form tạm
                
                let data = {
                    branch_id: this.newContainer.branch_id,
                    platform_id: this.newContainer.platform_id,
                    order_id: this.newContainer.order_id,
                    auxpacking_product_id: this.newContainer.auxpacking_product_id,
                    product_api_id: this.newContainer.product_api_id,
                    container_id: this.newContainer.selectedContainerId,
                    quantity: this.newContainer.quantity,
                    notes: this.newContainer.notes
                };
                console.log(data);
                // const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                // fetch('add-containera', {
                //     method: 'POST',
                //     headers: {
                //         'Content-Type': 'application/json',
                //         'X-CSRF-TOKEN': csrfToken // Đảm bảo bạn đã lấy và gửi CSRF token nếu cần
                //     },
                //     body: JSON.stringify(data)
                // })
                // .then(response => response.json())
                // .then(data => {
                //     console.log('Success:', data);
                //     this.updateProducts(data);
                //     this.showAddContainerForm = false;
                // })
                // .catch((error) => {
                //     console.error('Error:', error);
                // });
            },

            removeContainer(containerToRemove) {
                console.log(containerToRemove);
                if (containerToRemove.isSelected) {
                    alert('Thùng đã lấy, không thể xóa.');
                    return;
                }
                this.containerGroup[1] = this.containerGroup[1].filter(container => container.id !== containerToRemove.id);
                // Cập nhật giao diện ngay lập tức
                this.$nextTick(() => {
                        this.$dispatch('update-container-list');
                    });

                // fetch(`/api/containers/${container.id}`, {
                //     method: 'DELETE',
                //     headers: {
                //         'Content-Type': 'application/json',
                //         'Accept': 'application/json',
                //         'X-Requested-With': 'XMLHttpRequest', // Thêm này nếu là Laravel và sử dụng CSRF
                //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Thêm token CSRF nếu bạn đang dùng Laravel
                //     }
                // })
                // .then(response => response.json())
                // .then(data => {
                //     console.log('Success:', data);
                // })
                // .catch((error) => {
                //     console.error('Error:', error);
                // });
            }




        }
    }



</script>
@endpush