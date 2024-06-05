@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<x-conditional-content :condition="auth()->user()->hasRole('admin')">
<div class="container mx-auto px-2 sm:px-3 lg:px-4">
    <div id="productTable" class="w-full" x-data="productTable()">
        <div class="flex flex-wrap mx-auto mt-2 p-4 bg-white rounded shadow-md mb-2">
            <form id="searchProduct" @submit.prevent="submitForm" class="w-full mb-1">
                <div class="flex flex-wrap -mx-2">
                    <!-- Tìm sản phẩm -->
                    <div class="w-full sm:w-1/4 md:w-4/12 xl:w-8/24 px-2 mb-2 md:mb-0">
                        <label for="searchProductCode" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Tìm sản phẩm:</label>
                        <div class="relative" x-data="autocompleteProductSetup()" x-init="initAutocompleteProduct">
                            <input type="text" x-ref="productInput" id="searchProductCode" x-model="displayProductName" 
                                @blur="setTimeout(() => productSuggestions = [], 100)" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập sản phẩm">
                            <div class="absolute inset-y-0 right-0 flex items-center px-1">
                                <button type="button" @click="searchParams.searchProductCode = ''; displayProductName = ''" class="bg-gray-200 hover:bg-gray-300 text-gray-500 text-sm p-2 rounded-r-md">Xóa</button>
                            </div>
                            <div class="absolute z-10 w-full bg-white mt-1 rounded-md shadow-lg" x-show="productSuggestions.length > 0">
                                <ul>
                                    <template x-for="product in productSuggestions" :key="product.id">
                                        <li @click="selectProduct(product)" class="p-2 hover:bg-gray-100 cursor-pointer">
                                            <span x-text="product.name"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Trạng thái -->
                    <div class="w-full sm:w-1/4 md:w-3/12 xl:w-4/24 px-2 mb-1 md:mb-0">
                        <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Trạng thái:</label>
                        <select id="status" x-model="searchParams.status" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            <option value="1">Chưa lấy hàng</option>
                            <option value="2">Lấy chưa đủ</option>
                            <option value="3">Đã lấy hàng</option>
                        </select>
                    </div>
                    <!-- Kênh bán hàng -->
                    <div class="w-full sm:w-1/4 md:w-3/12 xl:w-4/24 px-2 mb-1 md:mb-0">
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
        @include('auxpackings.partial_auxpacking_product_table', compact(
            'branch_id',
            'products',
            'branches',
            'users',
            'platforms',
        ))
        <!-- <div class="mx-auto mt-2 max-w-full">
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
        </div> -->

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
    console.log(@json($productGroups));
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
        baseUrl: `{{ url('/auxpacking-product') }}/${branchId}`
    };
    document.addEventListener('alpine:init', () => {
        Alpine.data('productTable', () => ({
            searchParams: {
                searchProductCode: '',
                status: '',
                platform: ''
            },
            selectedItems: [],
            checkAll: false,
            selectedCount: 0,
            allContainers: @json($allContainers),
            products: @json($products),
            toggleModal(detail) {
                detail.openModal = !detail.openModal;
            },
            toggleAll() {
                if (!this.checkAll) {
                    this.selectedItems = this.products.map(item => item.id);//cần sửa code
                } else {
                    this.selectedItems = [];
                }
                this.updateCount();
            },
            updateCount() {
                this.selectedCount = this.selectedItems.length;
            },
            init() {
                Object.keys(this.products).forEach(productId => {
                    Object.values(this.products[productId]).forEach(containerGroup => {
                        containerGroup.forEach(container => {
                            container.isSelected = container.status === 1;
                        });
                    });
                    this.products[productId].showDetails = false;// Thêm showDetails cho mỗi nhóm sản phẩm
                });
                // console.log(this.products);
                // console.log(this.allContainers);
            },
            submitForm() {
                console.log(this.searchParams);
                this.fetchData(urls.baseUrl);
            },
            fetchData(baseUrl, params = this.searchParams) {
                const url = new URL(baseUrl);
                // url.searchParams.set('perPage', this.perPage);
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
                    console.log(data.products);
                    this.products = data.products;
                    this.$nextTick(() => {
                        this.init();//chạy lại để thêm showDetails, mới hiển thị đúng
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            },
            autocompleteProductSetup() {
                return {
                    allProducts: @json($allProducts),
                    productSuggestions: [],
                    displayProductName: '',
                    initAutocompleteProduct() {
                        console.log(this.allProducts);
                        this.$watch('displayProductName', (newValue) => {
                            if (newValue && newValue.length > 2) { // Chỉ bắt đầu tìm kiếm khi có ít nhất 3 ký tự
                                this.productSuggestions = this.allProducts.filter(product =>
                                    product.name.toLowerCase().includes(newValue.toLowerCase()) ||
                                    product.sku.toLowerCase().includes(newValue.toLowerCase())
                                );
                            } else {
                                this.productSuggestions = [];
                            }
                        });
                    },
                    selectProduct(product) {
                        this.displayProductName = product.name; // Hiển thị tên sản phẩm trong input
                        this.searchParams.searchProductCode = product.product_api_id; // Cập nhật giá trị tìm kiếm với product_api_id của sản phẩm đã chọn
                        this.productSuggestions = []; // Xóa các gợi ý sau khi sản phẩm được chọn
                    }
                }
            },
            sumSelectedQuantities(group) {
                var filteredGroup = group.slice(0, -1);
                var total = filteredGroup.reduce((total, container) => {
                    return total + (container[0].isSelected ? parseFloat(container[0].quantity) : 0);
                }, 0);
                return total;
            },
            totalGroupQuantity(group) {
                var filteredGroup = group.slice(0, -1);
                var total = filteredGroup.reduce((total, container) => {
                    var quantity = parseFloat(container[0].quantity);
                    return total + (isNaN(quantity) ? 0 : quantity);
                }, 0);
                return total;
            },
            remaining(group) {
                return this.totalGroupQuantity(group) - this.sumSelectedQuantities(group);
            },
            getBackgroundColor(group, showDetails) {
                const sumSelected = this.sumSelectedQuantities(group);
                const remaining = this.totalGroupQuantity(group) - sumSelected;
                let backgroundColorClass = '';
                if (sumSelected === 0) {
                    backgroundColorClass = 'bg-red-300';
                } else if (sumSelected > 0 && remaining !== 0) {
                    backgroundColorClass = 'bg-yellow-300';
                } else if (remaining === 0) {
                    backgroundColorClass = 'bg-green-500';
                }
                return showDetails ? `${backgroundColorClass} bg-blue-100` : backgroundColorClass;
            },
            selectAllContainers(group) {
                console.log(group);
                group.forEach(container => {
                    if (!container[0].isSelected) { // Kiểm tra nếu container chưa được chọn
                        container[0].isSelected = true; // Đặt trạng thái là được chọn
                        this.saveSelectContainer(container[0]); // Gửi dữ liệu đến server
                    }
                });
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
                })
                .catch(error => {
                    console.error('Error posting data:', error);
                    alert('Lỗi! Chưa lưu được.');
                });
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

                    openAddFormWithCopy(container) {
                        console.log(container);
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
                        console.log(addedContainer);
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
                            addedContainer.id = data.data.id;//gán mới Id vừa tạo
                            let containerId = data.data.container_id;
                            let productGroupId = addedContainer.product_api_id;
                            let group = this.products[productGroupId];
                            if (group.hasOwnProperty(containerId.toString())) {
                                group[containerId].push(addedContainer);
                            } else {
                                group[containerId] = [addedContainer];
                            }
                            // console.log(group);
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
                            this.containerGroup[1] = this.containerGroup[1].filter(container => container.id !== containerId);
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