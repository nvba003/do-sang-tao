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
            currentPage: 1,  // Ensure currentPage is part of your data model
            lastPage: 1,
            perPage: 15,
            links: '',
            searchParams: {
                searchProductCode: '',
                status: '',
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
                    this.products = data.products;
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
                    const branchId = this.product.branch_id;
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
            calculateTotalQtyDiff(productApiId, currentproductId) {
                let totalQuantity = 0;
                const details = this.productProcessings[productApiId] || [];
                details.forEach(detail => {
                    if (detail.product_id !== currentproductId) {
                        totalQuantity += parseFloat(detail.quantity);
                    }
                });
                return totalQuantity;
            },
            openProductDetails(productApiId, currentproductId) {
                this.selectedProductDetails = this.productProcessings[productApiId] || [];
                this.selectedProductDetails = this.selectedProductDetails.filter(detail => detail.product_id !== currentproductId);
                // this.openModalDiff = true;
                if (this.selectedProductDetails.length > 0) {
                    this.openModalDiff = true;
                } else {
                    this.openModalDiff = false;
                    console.log(`Không tìm thấy SP có ID ${productApiId} trong đơn hàng có ID ${currentproductId}`);
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
                // const maxId = this.product.details.reduce((max, detail) => Math.max(max, detail.id || 0), 0);
                this.product.details.push({
                    // id: maxId + 1,
                    id: Date.now(),// Trả về số milliseconds từ 1/1/1970 đến hiện tại
                    product_id: this.product.id,
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
            updateproductTotal(product) {
                let totalDiscount = 0;
                let subtotal = 0;
                product.details.forEach(detail => {
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
                const totalAmount = subtotal + (product.customer_shipping_fee - product.shipping_fee) - product.total_discount;
                const taxAmount = totalAmount * (product.tax / 100);
                const finalAmount = totalAmount + taxAmount - product.commission_fee;
                // Cập nhật các giá trị tổng cho đơn hàng
                product.subtotal = subtotal;
                product.total_amount = totalAmount;
                product.final_amount = finalAmount;
            },
            updateDetails() {
                console.log(this.product);
                // Kiểm tra trùng lặp product_api_id
                let productApiIds = new Set();
                for (const detail of this.product.details) {
                    if (productApiIds.has(detail.product_api_id)) {
                        alert('Lỗi: Có sản phẩm bị trùng lặp.');
                        return; // Ngưng thực thi nếu tìm thấy trùng lặp
                    }
                    productApiIds.add(detail.product_api_id);
                }
                const url = 'update-product';
                fetch(url, {
                    method: 'POST', // Hoặc 'PUT' nếu bạn đang cập nhật
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.product)
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
                const index = this.product.details.indexOf(detail);
                if (index !== -1) {
                    this.product.details.splice(index, 1);
                }
                this.updateproductTotal(this.product);//tính lại số tổng
            },
            updateInfo(product) {
                const updatedInfoproduct = {
                    id: product.id,  // Giả sử bạn cần ID để xác định đơn hàng cần cập nhật
                    carrier_id: product.product_process.carrier_id,
                    responsible_user_id: product.product_process.responsible_user_id,
                    customer_account_id: product.customer_account_id,
                    customer_id: product.customer_account.customer_id,
                    tracking_number: product.product_process.tracking_number,
                    product_type_id: product.product_type_id,
                    source_info: product.source_info,
                    status_id: product.product_process.status_id,
                    notes: product.notes
                };
                console.log(updatedInfoproduct);
                const url = "update-info-product";
                fetch(url, {
                    method: 'POST',  // Sử dụng 'PUT' nếu bạn đang cập nhật thông tin
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
            addFinance(financeAmount) {
                let newFinance = {
                        id: '',
                        product_id: '',
                        amount_due: '',
                        amount_paid: '',
                        amount_remaining: '',
                        created_at: '',
                    };
                if (this.product.finances.length === 0) {//nếu chưa có thanh toán
                    newFinance = {
                        id: Date.now(),
                        product_id: this.product.id,
                        amount_due: this.product.total_amount,
                        amount_paid: financeAmount,
                        amount_remaining: this.product.total_amount - financeAmount,
                        created_at: Date.now(),
                    };
                } else {
                    const lastFinance = this.product.finances[this.product.finances.length - 1];// Lấy thông tin tài chính cuối cùng và cập nhật
                    if (financeAmount === this.product.total_amount) {//nếu chọn thanh toán đủ
                        financeAmount = lastFinance.amount_remaining;//số tiền thanh toán là số tiền còn lại
                    }
                    newFinance = {
                        id: Date.now(),
                        product_id: this.product.id,
                        amount_due: lastFinance.amount_remaining,
                        amount_paid: financeAmount,
                        amount_remaining: lastFinance.amount_remaining - financeAmount,
                        created_at: Date.now(),
                    };
                }
                console.log(newFinance);
                const url = "add-finance-product";
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
                    this.product.finances.push(newFinance);
                    this.product.payment = data['payment'];//cập nhật giá trị trạng thái thanh toán
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
            },

            addBundle(product) {
                // Đặt các giá trị bundle là null
                product.bundle_id = null;
                product.bundles = [{
                    id: null,
                    name: '',
                    quantity: null
                }];
            },
            removeBundle(product) {
                if (!confirm('Bạn chắc chắn muốn xóa bundle này?')) {
                    return;
                }
                // Gửi request để xóa bundle
                fetch(`${urls.baseUrl}/bundles/${product.bundle_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    product.bundle_id = null;
                    product.bundles = [];
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi! Không thể xóa bundle.');
                });
            },
            saveBundle(bundle) {
                // Gửi request để lưu bundle
                fetch(`${urls.baseUrl}/bundles/${bundle.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(bundle)
                })
                .then(response => response.json())
                .then(data => {
                    alert('Đã lưu bundle thành công');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi! Không thể lưu bundle.');
                });
            },



        }));
    });

</script>
@endpush