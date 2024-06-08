@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<!-- <x-conditional-content :condition="auth()->user()->hasRole('admin') || auth()->user()->hasRole('productProcess')"> -->
<div class="container mx-auto px-2 sm:px-3 lg:px-4">
    <div id="productTable" class="w-full" x-data="productTable()">
        <div class="flex flex-wrap mx-auto mt-2 p-4 bg-white rounded shadow-md mb-2">
            <form id="searchProduct" @submit.prevent="submitForm" class="w-full mb-1">
                <div class="flex flex-wrap -mx-2">
                    <!-- Tìm sản phẩm -->
                    <div class="w-full sm:w-1/4 md:w-4/12 xl:w-8/24 px-2 mb-2 md:mb-0">
                        <label for="searchProductCode" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Tìm sản phẩm:</label>
                        <div class="relative">
                            <input type="text" id="searchProductCode" x-model="searchParams.searchProductCode" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập sản phẩm">
                            <div class="absolute inset-y-0 right-0 flex items-center px-1">
                                <button type="button" @click="searchParams.searchProductCode = ''" class="bg-gray-200 hover:bg-gray-300 text-gray-500 text-sm p-2 rounded-r-md">Xóa</button>
                            </div>
                        </div>
                    </div>
                    <!-- Nhóm -->
                    <div class="w-full sm:w-1/4 md:w-3/12 xl:w-4/24 px-2 mb-1 md:mb-0">
                        <label for="group" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Nhóm:</label>
                        <select id="group" x-model="searchParams.group" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            @foreach($productGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
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
        @include('products.partial_product_table')
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
  <div class="relative top-20 mx-auto p-5 bproduct w-96 shadow-lg rounded-md bg-white">
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
<!-- </x-conditional-content> -->
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

    const urls = {
        baseUrl: "{{ url('/product') }}",
    };

    document.addEventListener('alpine:init', () => {
        Alpine.data('productTable', () => ({
            products: [],
            allProducts: @json($allProducts),
            categories: @json($categories),
            productGroups: @json($productGroups),
            userRole: @json(auth()->user()->roles()->first()->name),
            currentPage: 1,  // Ensure currentPage is part of your data model
            lastPage: 1,
            perPage: 15,
            links: '',
            searchParams: {
                searchProductCode: '',
                group: '',
            },
            selectedItems: [],
            checkAll: false,
            selectedCount: 0,
            toggleModal(detail) {
                detail.openModal = !detail.openModal;
            },
            toggleAll() {
                if (!this.checkAll) {
                    this.selectedItems = this.products.map(item => item.id);
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
                this.products = initialData.products;
                this.links = initialData.links;
                // this.fetchData(urls.baseUrl);
                this.products.forEach(item => {
                    item.showDetails = false;
                });
                // Watch for changes to currentPage and fetch new data accordingly
                this.$watch('currentPage', (newPage) => {
                    this.fetchData(`${urls.baseUrl}?page=${newPage}`);
                });
                console.log(this.products);
                console.log(this.userRole);
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
                    this.products = data.products;
                    this.links = data.links;
                    console.log(this.products);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            },
            submitForm() {
                //console.log(this.searchParams);
                this.fetchData(urls.baseUrl);
            },
            getCategory(id) {
                id = parseInt(id);  // Chuyển đổi id sang số nếu nó là chuỗi
                let category = this.categories.find(c => c.id === id);
                return category ? category.name : '_';
            },
            getGroup(id) {
                id = parseInt(id);  // Chuyển đổi id sang số nếu nó là chuỗi
                let group = this.productGroups.find(c => c.id === id);
                return group ? group.name : '_';
            },
            updateInfo(product) {
                // console.log(product);
                const updatedInfoproduct = {
                    id: product.id,
                    sku: product.sku,
                    name: product.name,
                    category_id: product.category_id,
                    product_group_id: product.product_group_id,
                };
                console.log(updatedInfoproduct);
                const url = "{{ route('products.updateInfo') }}";
                fetch(url, {
                    method: 'PUT',  // Sử dụng 'PUT' nếu bạn đang cập nhật thông tin
                    headers: {
                        'Content-Type': 'application/json',
                        // Thêm token nếu API yêu cầu xác thực
                        // 'Authorization': 'Bearer your-token-here',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(updatedInfoproduct)
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
            updateSizeWeight(product) {
                // console.log(product);
                const updatedInfoproduct = {
                    id: product.id,
                    length: product.length,
                    height: product.height,
                    width: product.width,
                    weight: product.weight,
                };
                console.log(updatedInfoproduct);
                const url = "{{ route('products.updateSizeWeight') }}";
                fetch(url, {
                    method: 'PUT',  // Sử dụng 'PUT' nếu bạn đang cập nhật thông tin
                    headers: {
                        'Content-Type': 'application/json',
                        // Thêm token nếu API yêu cầu xác thực
                        // 'Authorization': 'Bearer your-token-here',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(updatedInfoproduct)
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
            },

            addBundle(product) {
                console.log(product);
                product.bundle_id = Date.now();
                product.bundle = {
                    id: '',
                    name: product.name,
                    price: product.price || null,
                    type: 1,
                    description: product.description || null,
                    bundle_items: []
                };
            },
            removeBundle(product) {
                if (!confirm('Bạn chắc chắn muốn xóa combo này?')) {
                    return;
                }
                // Gửi request để xóa bundle
                fetch(`${urls.baseUrl}/bundle/${product.bundle.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();  // Chuyển kết quả trả về dưới dạng JSON
                })
                .then(data => {
                    product.bundle_id = null;
                    product.bundle = [];
                    toggleModal(true); // Hiển thị modal khi có thông báo thành công
                        setTimeout(function() {
                            toggleModal(false); // Ẩn modal sau 500ms
                        }, 500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi! Không thể xóa combo.');
                });
            },
            addBundleItem(bundle) {
                bundle.bundle_items.push({ id: Date.now(), product_api_id: '', quantity: 1 });
            },
            autocompleteProductSetup(item) {
                return {
                    productSuggestions: [],
                    displayProductName: item.product ? item.product.name : '',
                    initAutocompleteProduct() {
                        // console.log(this.allProducts);
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
                    selectProduct(product,item) {
                        this.displayProductName = product.name; // Hiển thị tên sản phẩm trong input
                        item.product_api_id = product.product_api_id; // Cập nhật giá trị tìm kiếm với product_api_id của sản phẩm đã chọn
                        this.productSuggestions = []; // Xóa các gợi ý sau khi sản phẩm được chọn
                    }
                }
            },
            removeBundleItem(item, itemIndex) {
                item.splice(itemIndex, 1);
            },
            saveBundleItems(bundle, productId) {
                bundle.product_id =  productId;
                console.log(bundle);
                fetch(`${urls.baseUrl}/bundle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(bundle)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();  // Chuyển kết quả trả về dưới dạng JSON
                })
                .then(data => {
                    console.log('Success:', data);
                    bundle.id = data.bundle_id;
                    // console.log(this.products);
                    toggleModal(true); // Hiển thị modal khi có thông báo thành công
                        setTimeout(function() {
                            toggleModal(false); // Ẩn modal sau 500ms
                        }, 500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi! Không thể lưu combo.');
                });
            },



        }));
    });

</script>
@endpush