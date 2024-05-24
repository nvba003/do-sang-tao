@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@section('content')
<x-conditional-content :condition="auth()->user()->hasRole('admin')">
<div class="container mx-auto px-2 sm:px-3 lg:px-4">
    <button id="toggle-btn" class="bg-gray-800 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded my-2" onclick="toggleScanningArea()">Show/Hide Scanning Area</button>
    <!-- Scanning and Summary Area -->
    <div id="scanning-area" class="hidden rounded bg-white shadow">
        <div class="flex flex-wrap -mx-3 w-full">
            <!-- Scanner Column -->
            <div class="w-full lg:w-1/2 px-3">
                <div class="p-5">
                    <div class="mt-1">
                        <button id="btn-scan" class="w-50 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                onclick="toggleScan()">Quét</button>
                    </div>
                    <div id="reader" class="w-full"></div>
                </div>
            </div>
            <!-- Summary Column -->
            <div id="scanInfo" class="w-full lg:w-1/2 px-3 mb-6 mt-6 hidden">
                <div class="bg-white p-5 rounded-lg shadow lg:mt-10">
                    <h1 class="text-xl font-bold text-center mb-4">Thông tin quét</h1>
                    <p class="text-center">Tổng số quét: <span id="total-scanned">0</span></p>
                    <p class="text-center">Sources: <span id="source-summary">N/A</span></p>
                </div>
            </div>
        </div>
        <!-- Scan Results Table -->
        <div class="bg-white p-5 rounded-lg shadow w-full">
            <table class="table-auto w-full text-sm">
                <thead class="bg-blue-500">
                    <tr class="text-white" >
                        <th class="border px-4 py-2">Ngày quét</th>
                        <th class="border px-4 py-2">Mã vận đơn</th>
                        <th class="border px-4 py-2">Mã đơn</th>
                        <th class="border px-4 py-2">Kênh</th>
                        <th class="border px-4 py-2">Người quét</th>
                        <th class="border px-4 py-2">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="result-body">
                </tbody>
            </table>
        </div>
    </div>

    <button onclick="playBeepSound()">Test Beep</button>
    <button onclick="playBeepErrorSound()">Test Beep Error</button>
    <button onclick="playErrorSound()">Test Error</button>

    <div id="history-area">
        <div id="searchForm" class="flex flex-wrap mx-auto p-4 bg-white rounded shadow-md mb-2">
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
            <table id="containerTable" class="w-full bg-white border border-gray-200 rounded-lg">
                <thead class="text-white bg-gray-500">
                    <tr>
                        <th scope="col" class="w-1/24 px-2 py-3 mt-1 text-center text-xs md:text-sm hidden sm:block font-semibold uppercase tracking-wider">
                            <input type="checkbox" id="checkAll">
                        </th>
                        <th scope="col" class="w-1/24 px-2 py-3 text-left text-center text-xs md:text-sm font-semibold uppercase tracking-wider"></th>
                        <th scope="col" class="w-5/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Ngày quét</th>
                        <th scope="col" class="w-4/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Mã vận chuyển</th>
                        <th scope="col" class="w-4/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Mã đơn</th>
                        <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Kênh</th>
                        <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Người quét</th>
                        <th scope="col" class="w-2/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                    @include('auxpackings.partial_auxpacking_scan_table', ['scans' => $scans, 'users' => $users])
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
    function playBeepSound() {
        var audio = document.getElementById('beep-sound');
        audio.play();
    }
    function playBeepErrorSound() {
        var audio = document.getElementById('beep-error-sound');
        audio.play();
    }
    function playErrorSound() {
        var audio = document.getElementById('error-sound');
        audio.play();
    }
    var html5QrcodeScanner;
    var scanning = false;
    function onScanSuccess(decodedText, decodedResult) {
        fetch('store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ tracking_number: decodedText })
        })
        .then(response => response.json())
        .then(data => {
            // Update the result table
            var resultBody = document.getElementById('result-body');
            var row = resultBody.insertRow(0);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            cell1.innerHTML = decodedText;
            cell2.innerHTML = data.message;
            cell3.innerHTML = new Date().toLocaleString();

            // Update the total scanned count
            var totalScanned = document.getElementById('total-scanned');
            totalScanned.textContent = parseInt(totalScanned.textContent) + 1;

            console.log(data);
            console.log(data.status);
            // Handle different response statuses
            if (data.status === 404) {//lỗi không tìm thấy đơn
                console.log('Không tìm thấy đơn')
                playErrorSound();
            } else if (data.status === 409) {//lỗi đơn đã quét
                playBeepErrorSound()
                //notify(); // Assuming notify() shows a modal with the message
            } else if (data.status === 200) {//thành công
                playBeepSound();
            }
        })
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

    function toggleScanningArea() {
        var scanningArea = document.getElementById('scanning-area');
        scanningArea.classList.toggle('hidden');
    }

    function startScan() {
        html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    }

    $(document).ready(function() {
        // Xử lý form tìm kiếm
        let currentSearchParams = "";
        let currentPerPage = "";
        let perPage = $('#perPage').val();
        const branchId = {{ $branch_id }};// Truyền giá trị branch_id từ Blade vào JavaScript
        var scans = @json($scans)['data'];
        console.log(scans);

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

        const baseUrl = `{{ url('/auxpacking-scan') }}/${branchId}`;
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