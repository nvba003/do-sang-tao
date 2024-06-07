@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@section('content')
<x-conditional-content :condition="auth()->user()->hasRole('admin') || auth()->user()->hasRole('packing')">
<div class="container mx-auto px-2 sm:px-3 lg:px-4">
  <div class="w-full" x-data="orderTable()" x-init="init()">
    <button class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded my-2"
            @click="toggleScanningArea()">Hiện/Ẩn Quét Đơn</button>
    <!-- Scanning and Summary Area -->
    <div id="scanning-area" class="rounded bg-white shadow" x-show="showScan">
        <div class="flex flex-wrap -mx-3 w-full">
            <!-- Scanner Column -->
            <div class="w-full lg:w-1/2 px-3">
                <div class="px-4">
                    <div class="mt-1">
                        <button id="btn-scan" class="w-50 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mt-1 rounded"
                                onclick="toggleScan()">Quét</button>
                    </div>
                    <div id="reader" class="w-full"></div>
                </div>
            </div>
            <!-- Summary Column -->
            <div id="scanInfo" class="w-full lg:w-1/2 px-3 my-2 hidden">
                <div class="bg-white p-3 rounded-lg shadow lg:mt-10 flex flex-wrap" x-data="{ store: $store.scannerStore }">
                    <!-- Cột Thông tin chung -->
                    <div class="w-1/2">
                        <h1 class="text-xl font-bold text-center mb-4">Thông tin quét</h1>
                        <div>Tổng đã quét: <span x-text="store.totalScanned"></span></div>
                        <div>Quét thành công: <span x-text="store.successfulScans"></span></div>
                        <div>Lỗi không có thông tin: <span x-text="store.errorStats['404']"></span></div>
                        <div>Lỗi do đơn đã quét: <span x-text="store.errorStats['409']"></span></div>
                    </div>
                    <!-- Cột Thống kê theo kênh bán hàng -->
                    <div class="w-1/2 border-l" x-show="Object.keys(store.platformStats).length > 0">
                        <h3 class="text-xl font-bold text-center mb-4">Quét theo kênh BH</h3>
                        <template x-for="(count, platformId) in store.platformStats">
                            <div x-text="`${store.getPlatformName(platformId)}: ${count}`" class="mb-2 ml-2"></div>
                        </template>
                    </div>
                    <!-- <div x-show="Object.keys(store.platformStats).length > 0">
                        <h3>Scans by Platform:</h3>
                        <template x-for="(count, platformId) in store.platformStats" :key="platformId">
                            <div x-text="`Platform ${store.getPlatformName(platformId)}: ${count}`"></div>
                        </template>
                    </div> -->
                </div>
            </div>
        </div>
        <!-- Scan Results Table -->
        <div class="bg-white p-4 rounded-lg shadow w-full overflow-x-auto" x-data="{ scanResults: Alpine.store('scannerStore').scanResults }">
            <table class="table-auto w-full text-xs sm:text-sm">
                <thead class="bg-blue-500">
                    <tr class="text-white">
                        <th class="border px-4 py-2">Ngày quét</th>
                        <th class="border px-4 py-2">Mã vận đơn</th>
                        <th class="border px-4 py-2">Thông tin</th>
                        <th class="border px-4 py-2">Mã đơn</th>
                        <th class="border px-4 py-2">Kênh</th>
                        <th class="border px-4 py-2">Người quét</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="result in scanResults" :key="result.id">
                        <tr>
                            <td class="px-2" x-text="new Date(result.time).toLocaleString()"></td>
                            <td class="px-2" x-text="result.tracking_number"></td>
                            <td class="px-2" :class="{
                                'text-green-500': result.status === 200,
                                'text-red-500': result.status === 409,
                                'text-gray-800': result.status === 404
                            }" x-text="result.message"></td>
                            <td class="px-2" x-text="result.order.order_code"></td>
                            <td class="px-2" x-text="result.platform.name"></td>
                            <td class="px-2" x-text="result.user.name"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

    </div>

    <div id="history-area">
        <div class="flex flex-wrap mx-auto mt-2 p-4 bg-white rounded shadow-md mb-2">
            <form id="searchOrder" @submit.prevent="submitForm" class="w-full mb-1">
                <div class="flex flex-wrap -mx-2">
                    <!-- Tìm đơn hàng -->
                    <div class="w-full sm:w-1/2 md:w-3/12 xl:w-5/24 px-2 mb-2 md:mb-0">
                        <label for="searchOrderCode" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Tìm đơn hàng:</label>
                        <div class="relative">
                            <input type="text" id="searchOrderCode" x-model="searchParams.searchOrderCode" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập mã đơn hàng">
                            <div class="absolute inset-y-0 right-0 flex items-center px-1">
                                <button type="button" @click="searchParams.searchOrderCode = ''" class="bg-gray-200 hover:bg-gray-300 text-gray-500 text-sm p-2 rounded-r-md">Xóa</button>
                            </div>
                        </div>
                    </div>
                    <!-- Tìm mã vận chuyển -->
                    <div class="w-full sm:w-1/2 md:w-3/12 xl:w-5/24 px-2 mb-2 md:mb-0">
                        <label for="searchTrackingNumber" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Tìm mã vận chuyển:</label>
                        <div class="relative">
                            <input type="text" id="searchTrackingNumber" x-model="searchParams.searchTrackingNumber" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập mã vận chuyển">
                            <div class="absolute inset-y-0 right-0 flex items-center px-1">
                                <button type="button" @click="searchParams.searchTrackingNumber = ''" class="bg-gray-200 hover:bg-gray-300 text-gray-500 text-sm p-2 rounded-r-md">Xóa</button>
                            </div>
                        </div>
                    </div>
                    <!-- Trạng thái -->
                    <div class="w-full sm:w-2/12 xl:w-3/24 px-2 mb-1 md:mb-0">
                        <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Trạng thái:</label>
                        <select id="status" x-model="searchParams.status" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            <option value="2">Chưa giao</option>
                            <option value="3">Đã giao</option>
                        </select>
                    </div>
                    <!-- User -->
                    <div class="w-full sm:w-1/3 md:w-2/12 xl:w-3/24 px-2 mb-1 md:mb-0">
                        <label for="user" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Người quét:</label>
                        <select id="user" x-model="searchParams.user" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Kênh bán hàng -->
                    <div class="w-full sm:w-1/3 md:w-2/12 xl:w-3/24 px-2 mb-1 md:mb-0">
                        <label for="platform" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Kênh:</label>
                        <select id="platform" x-model="searchParams.platform" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">Chọn</option>
                            @foreach($platforms as $platform)
                                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Nút Tìm -->
                    <div class="w-full sm:w-1/4 md:w-2/12 xl:w-2/24 px-2 py-1 mt-0 sm:mt-3 flex items-end">
                        <button type="submit" class="bg-blue-500 text-white py-2 px-2 rounded hover:bg-blue-600 w-full">Tìm</button>
                    </div>
                </div>
            </form>
        </div>
        @include('auxpackings.partial_auxpacking_scan_table', compact(
                'branch_id',
                'orders',
                'users',
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
</div>
</x-conditional-content>
@endsection

@push('scripts')
<script>
    var html5QrcodeScanner;
    var scanning = false;
    function onScanSuccess(decodedText, decodedResult) {
        Alpine.store('scannerStore').addScanResult(decodedText, decodedResult);
        // fetch('store', {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        //     },
        //     body: JSON.stringify({ tracking_number: decodedText })
        // })
        // .then(response => response.json())
        // .then(data => {
        //     // Update the result table
        //     var resultBody = document.getElementById('result-body');
        //     var row = resultBody.insertRow(0);
        //     var cell1 = row.insertCell(0);
        //     var cell2 = row.insertCell(1);
        //     var cell3 = row.insertCell(2);
        //     cell1.innerHTML = decodedText;
        //     cell2.innerHTML = data.message;
        //     cell3.innerHTML = new Date().toLocaleString();

        //     // Update the total scanned count
        //     var totalScanned = document.getElementById('total-scanned');
        //     totalScanned.textContent = parseInt(totalScanned.textContent) + 1;

        //     console.log(data);
        //     console.log(data.status);
        //     // Handle different response statuses
        //     if (data.status === 404) {//lỗi không tìm thấy đơn
        //         console.log('Không tìm thấy đơn')
        //         playErrorSound();
        //     } else if (data.status === 409) {//lỗi đơn đã quét
        //         playBeepErrorSound()
        //         //notify(); // Assuming notify() shows a modal with the message
        //     } else if (data.status === 200) {//thành công
        //         playBeepSound();
        //     }
        // })
        // .catch(error => {
        //     console.error('Error:', error);
        //     playErrorSound();
        // });
        //html5QrcodeScanner.clear();
    }

    function toggleScan() {
        if (!scanning) {
            html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
            html5QrcodeScanner.render(onScanSuccess);
            document.getElementById('btn-scan').textContent = 'Ngừng';
            scanning = true;
        } else {
            html5QrcodeScanner.clear();
            document.getElementById('btn-scan').textContent = 'Quét';
            scanning = false;
        }
        var scanInfo = document.getElementById('scanInfo');
        scanInfo.classList.toggle('hidden');
    }

    function startScan() {
        html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
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

    const branchId = {{ $branch_id }};// Truyền giá trị branch_id từ Blade vào JavaScript
    const urls = {
        baseUrl: `{{ url('/auxpacking-scan') }}/${branchId}`
    };
    const now = new Date();
    const tomorrow = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
    const expiration = tomorrow.getTime(); // Thời gian hết hạn là 0h ngày hôm sau
    function checkAndClearStorage() {
        const expiration = localStorage.getItem('expiration');
        const now = Date.now(); // Lấy thời gian hiện tại tính bằng milliseconds
        if (expiration && now >= parseInt(expiration)) {
            // Nếu thời gian hiện tại đã đạt hoặc vượt quá thời điểm hết hạn, xóa dữ liệu
            localStorage.removeItem('scanData');
            localStorage.removeItem('expiration');
            console.log('LocalStorage cleared due to expiration.');
        } else {
            console.log('LocalStorage not expired yet.');
        }
    }
    checkAndClearStorage();

    document.addEventListener('alpine:init', () => {
        Alpine.store('scannerStore', {
            scanResults: [],
            platformNames: @json($platforms->pluck('name', 'id')),
            scannedTrackingNumbers: new Set(), // Set này sẽ lưu trữ các tracking_number đã quét
            totalScanned: 0,
            successfulScans: 0,
            errorStats: {
                '404': 0, // Không tìm thấy đơn hàng
                '409': 0, // Đơn hàng đã quét
            },
            platformStats: {},
            addScanResult(decodedText, decodedResult) {
                fetch('{{ route('scans.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ tracking_number: decodedText })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    this.handleScanData(decodedText, data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.playErrorSound();
                });
            },
            handleScanData(decodedText, data) {
                this.scanResults.unshift({
                    time: new Date().toISOString(),
                    tracking_number: data.tracking_number,
                    order: data.data ? data.data.order : "_",
                    id: data.data ? data.data.id : "_",
                    platform: data.data ? data.data.platform : "_",
                    user: data.data ? data.data.user : "_",
                    message: data.message, // Thông báo từ server
                    status: data.status
                });
                // Kiểm tra nếu tracking_number đã được quét
                if (this.scannedTrackingNumbers.has(data.tracking_number)) {
                    if (data.status === 200) {
                        this.playBeepSound();
                    } else {
                        if (data.status === 404) {
                            this.playErrorSound();
                        } else if (data.status === 409) {
                            this.playBeepErrorSound();
                        }
                    }
                    return; // Nếu đã quét thì không làm gì cả
                }
                this.scannedTrackingNumbers.add(data.tracking_number);// Thêm tracking_number vào set
                this.totalScanned += 1;
                if (data.status === 200) {
                    this.successfulScans += 1;
                    this.incrementPlatformCount(data.data.platform_id);
                    this.playBeepSound();
                } else {
                    this.incrementErrorCount(data.status);
                    if (data.status === 404) {
                        this.playErrorSound();
                    } else if (data.status === 409) {
                        this.playBeepErrorSound();
                    }
                }
                //console.log(expiration);
                localStorage.setItem('scanData', JSON.stringify({scanResults:this.scanResults, totalScanned:this.totalScanned, successfulScans:this.successfulScans, errorStats:this.errorStats, platformStats:this.platformStats}));
                localStorage.setItem('expiration', expiration);
            },
            incrementPlatformCount(platformId) {
                if (this.platformStats[platformId]) {
                    this.platformStats[platformId] += 1;
                } else {
                    this.platformStats[platformId] = 1;
                }
            },
            incrementErrorCount(statusCode) {
                if (this.errorStats[statusCode] !== undefined) {
                    this.errorStats[statusCode] += 1;
                }
            },
            getPlatformName(platformId) {
                return this.platformNames[platformId] || '_'; // Lấy tên platform hoặc trả về 'Unknown'
            },
            playBeepSound() {
                var audio = document.getElementById('beep-sound');
                audio.play();
            },
            playBeepErrorSound() {
                var audio = document.getElementById('beep-error-sound');
                audio.play();
            },
            playErrorSound() {
                var audio = document.getElementById('error-sound');
                audio.play();
            },

        });
        Alpine.data('orderTable', () => ({
            orders: [],
            currentPage: 1,  // Ensure currentPage is part of your data model
            lastPage: 1,
            perPage: 15,
            links: '',
            searchParams: {
                searchOrderCode: '',
                searchTrackingNumber: '',
                user: '',
                platform: '',
                status: '',
            },
            selectedItems: [],
            checkAll: false,
            selectedCount: 0,
            showScan: false,
            toggleScanningArea() {
                this.showScan = !this.showScan;
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
                this.fetchData(urls.baseUrl);
                // Watch for changes to currentPage and fetch new data accordingly
                this.$watch('currentPage', (newPage) => {
                    this.fetchData(`${urls.baseUrl}?page=${newPage}`);
                });
                // console.log(this.orders);
                // console.log(this.links);
                // Đặt watcher để tự động cập nhật khi store thay đổi
                this.$watch(() => Alpine.store('scannerStore').scanResults, (newScanResults) => {
                    this.syncOrdersFromStore();
                });
            },
            syncOrdersFromStore() {
                // Thêm mới vào orders từ scanResults trong store mà không làm mất đi dữ liệu ban đầu
                const currentOrderIds = this.orders.map(order => order.id); // Lấy danh sách ID của các đơn hàng hiện tại
                Alpine.store('scannerStore').scanResults.forEach(scan => {
                    if (!currentOrderIds.includes(scan.id)) { // Kiểm tra xem ID đơn hàng đã tồn tại chưa
                        let newOrder = {
                            created_at: scan.time,
                            id: scan.id,
                            tracking_number: scan.tracking_number,
                            order: scan.order,
                            platform: scan.platform,
                            user: scan.user,
                        }
                        this.orders.unshift(newOrder);
                        currentOrderIds.push(scan.id); // Cập nhật danh sách ID để không thêm trùng lặp
                    }
                });
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
                //console.log(params);
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Mark the request as AJAX
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    //console.log(response);
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
            formatDate(dateString) {
                const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
                return new Date(dateString).toLocaleString('vi-VN', options);
            },
            scanClass(order) {
                if (order.auxpacking_order) {
                    const status = parseFloat(order.auxpacking_order.status);
                    if (status === 3) {
                        return 'bg-green-500';
                    }
                }
                if (order.showDetails) {
                    return 'bg-blue-100';
                } else {
                    return 'bg-white';
                }
            },
            deleteScan(orderId) {
                if (!confirm('Bạn có chắc chắn muốn xóa đơn quét này không?')) {
                    return;
                }
                fetch('{{ route('auxPackingScan.remove') }}', {
                    method: 'DELETE', // Sử dụng method DELETE cho RESTful
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ scanId: orderId })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    console.log(response);
                    return response.json();
                })
                .then(data => {
                    console.log(data.orders);
                    this.orders = this.orders.filter(order => order.id !== orderId);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi chưa xóa được');
                });
            },
            deliveryHandoff() {
                // const selectedOrders = this.orders.filter(order => this.selectedItems.includes(order.id));
                if (this.selectedItems.length === 0) {
                    alert('Vui lòng chọn ít nhất một đơn hàng để giao.');
                    return;
                }
                console.log(this.selectedItems);
                fetch('{{ route('auxPackingContainer.updateStatuses') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        selectedOrderIds: this.selectedItems,
                        newStatus: 3
                    })
                })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error('Something went wrong');
                })
                .then(data => {
                    //console.log(data);
                    const orderIds = data.orderIds;
                    this.orders.forEach(order => {
                        if (orderIds.includes(order.order_id)) {
                            order.auxpacking_order.status = 3;
                        }
                    });
                    this.selectedItems = [];
                    this.checkAll = false;
                    this.selectedCount = 0;
                    toggleModal(true); // Hiển thị modal khi có thông báo thành công
                    setTimeout(function() {
                        toggleModal(false); // Ẩn modal sau 500ms
                    }, 500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi khi cập nhật đơn hàng.');
                });
            
            },

        }));
    });
    
</script>
@endpush