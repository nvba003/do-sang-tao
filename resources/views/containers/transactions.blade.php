@extends('layouts.app')
@php
use Carbon\Carbon;
@endphp

@section('content')
<div id="formContainer" class="container mx-auto mt-3">
    <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf

        <div class="flex flex-wrap -mx-3 mb-2">
            <!-- Gộp Chọn Chi Nhánh và Loại Giao Dịch -->
            <div class="w-full md:w-1/2 px-3 mb-2 md:mb-0">
                <label for="branch_id" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Chọn Chi Nhánh</label>
                <select class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="branch_id" name="branch_id" required>
                    <option value="">Chọn chi nhánh</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-1/2 px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Lựa chọn</label>
                <div class="flex">
                    <label class="btn flex-1 text-sm bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded-l">
                        <input type="radio" name="type" id="type_in" value="Nhap" autocomplete="off" checked> Nhập
                    </label>
                    <label class="btn flex-1 text-sm bg-red-500 hover:bg-red-700 text-white py-2 px-4">
                        <input type="radio" name="type" id="type_out" value="Xuat" autocomplete="off"> Xuất
                    </label>
                    <label class="btn flex-1 text-sm bg-green-500 hover:bg-green-700 text-white py-2 px-4 rounded-r">
                        <input type="radio" name="type" id="type_check" value="Kiem" autocomplete="off"> Kiểm
                    </label>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap -mx-3 mb-2">
            <!-- Gộp Mã Thùng và TransactionTypes -->
            <div class="w-full md:w-1/2 px-3 mb-2 md:mb-0">
                <label for="container_id" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Mã Thùng</label>
                <input type="text" class="appearance-none block w-full bg-gray-200 text-gray-700 border rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="container_id" name="container_id" placeholder="Nhập mã thùng" required>
            </div>
            <div class="w-full md:w-1/2 px-3">
                <label for="transaction_type_id" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Loại Giao Dịch</label>
                <select class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="transaction_type_id" name="transaction_type_id" required>
                    <option value="">Chọn loại</option>
                    @foreach($transactionTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex flex-wrap -mx-3 mb-2">
            <!-- Gộp Số Lượng và Ghi Chú -->
            <div class="w-full md:w-1/2 px-3 mb-2 md:mb-0">
                <label for="quantity" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Số Lượng</label>
                <div class="flex">
                    <button type="button" class="text-sm bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-l" onclick="decreaseQuantity()">-</button>
                    <input type="number" class="w-full text-center border-t border-b border-gray-300 py-2" id="quantity" name="quantity" value="1" min="1" required>
                    <button type="button" class="text-sm bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-r" onclick="increaseQuantity()">+</button>
                </div>
            </div>
            <div class="w-full md:w-1/2 px-3">
                <label for="notes" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Ghi Chú</label>
                <textarea class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="notes" name="notes" rows="1"></textarea>
            </div>
        </div>

        <!-- Nút Thực Hiện -->
        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Lưu giao dịch</button>
    </form>
</div>

<div id="searchForm" class="container mx-auto mt-3">
    <form id="searchContainer" action="" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        <div class="flex">
            <input type="text" class="flex-grow appearance-none border rounded-l py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập mã thùng" name="container_id" id="search_container_id">
            <button class="btn bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded-r" type="submit">Tìm Kiếm</button>
            <button class="btn ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded" type="button" onclick="clearSearch()">Xóa Tìm Kiếm</button>
        </div>
    </form>
</div>

<div class="container mx-auto mt-4">
    <div class="flex flex-col">
        <nav aria-label="Page navigation example">
            <ul class="flex list-reset pl-0 rounded my-2">
                <!-- Pagination here -->
            </ul>
        </nav>
        <div class="shadow overflow-hidden rounded border-b border-gray-200">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-800 text-white">
                    <tr>
                      <th class="th-common">+</th>
                      <th class="th-common">Ngày</th>
                      <th class="th-common">ID thùng</th>
                      <th class="th-common">Số Lượng</th>
                      <th class="th-common">Loại</th>
                      <th class="th-common">Tồn</th>
                      <th class="th-common">Tên Sản Phẩm</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data rows here -->
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection


@push('scripts')
<script>
    var transactions = @json($transactions);
    var currentData = transactions; // Biến toàn cục giữ dữ liệu hiện tại
    var currentType = 'transaction';
    var origin_path = "{{ route('transactions.data') }}"; // Định nghĩa URL cho AJAX request
    var path = "{{ route('transactions.searchContainer') }}"; // Định nghĩa URL cho AJAX request

    function increaseQuantity() {
        var quantityInput = document.getElementById('quantity');
        var currentValue = parseInt(quantityInput.value);
        quantityInput.value = currentValue + 1;
    }

    function decreaseQuantity() {
        var quantityInput = document.getElementById('quantity');
        var currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    }
</script>
@endpush

@push('scripts')
    <script>
    function updateBackground(type) {
        var container = document.getElementById('formContainer');
        container.classList.remove('alert-primary', 'alert-danger', 'alert-success'); // Xóa các class cũ
        if(type === 'Nhap') {
            container.classList.add('alert-primary'); // Thêm class mới
        } else if(type === 'Xuat') {
            container.classList.add('alert-danger');
        } else if(type === 'Kiem') {
            container.classList.add('alert-success');
        }
    }

    // Gắn hàm updateBackground vào sự kiện click của từng nút
    document.getElementById('type_in').addEventListener('click', function() { updateBackground('Nhap'); });
    document.getElementById('type_out').addEventListener('click', function() { updateBackground('Xuat'); });
    document.getElementById('type_check').addEventListener('click', function() { updateBackground('Kiem'); });
    </script>
@endpush

@push('scripts')
    <script>
    //kiểm tra validate dữ liệu
    document.addEventListener('DOMContentLoaded', function () {
        const transactionForm = document.getElementById('transactionForm');
        const branchId = document.getElementById('branch_id');
        const containerId = document.getElementById('container_id');
        const transactionTypeId = document.getElementById('transaction_type_id');
        const quantity = document.getElementById('quantity');
        const productId = document.getElementById('product_id');
        const productInfoDisplay = document.getElementById('product_info');
        const errorMessageDisplay = document.getElementById('error_message');
        containerId.addEventListener('change', function() {
            const containerIdValue = this.value;
            fetch('api/inventory-transactions/product/' + containerIdValue)
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.status === 'success') {
                        productInfoDisplay.style.display = 'block';
                        errorMessageDisplay.style.display = 'none';
                        productInfoDisplay.textContent = `SP: ${data.productName}`;
                        productId.value = data.productId; // Cập nhật giá trị của trường ẩn 
                    } else {
                        errorMessageDisplay.style.display = 'block';
                        productInfoDisplay.style.display = 'none';
                        errorMessageDisplay.textContent = data.message;
                        productId.value = ''; // Cập nhật giá trị của trường ẩn 
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        transactionForm.addEventListener('submit', function (e) {
            let valid = true;
            let messages = [];
            if (!branchId.value) {
                valid = false;
                messages.push('Vui lòng chọn chi nhánh.');
            }
            if (!transactionTypeId.value) {
                valid = false;
                messages.push('Vui lòng chọn loại giao dịch.');
            }
            if (!quantity.value || quantity.value < 1) {
                valid = false;
                messages.push('Số lượng phải lớn hơn 0.');
            }
            if (!productId.value) {
                valid = false;
                messages.push('Chưa có thông tin sản phẩm.');
            }
            if (!valid) {
                e.preventDefault(); // Ngăn form submit nếu có lỗi
                alert(messages.join('\n')); // Hiển thị thông báo lỗi
            } else {
                // Nếu không có lỗi, form sẽ được submit bình thường
                transactionForm.submit();
            }
        });

    });

    </script>
@endpush

@push('scripts')
<script>
    function clearSearch() {
        // Xóa giá trị của ô tìm kiếm và thực hiện một hành động tìm kiếm / reset nào đó nếu cần
        document.getElementById('search_container_id').value = '';
        refeshData();
    }
</script>
@endpush

@push('scripts')
<script>
    // Sử dụng delegation event để gán sự kiện click cho các nút mở rộng, hiện tại và tương lai
    $('#myTable').on('click', '.expand-button', function() {
            var targetId = $(this).data('target');
            $(targetId).toggle(); // Toggle hiển thị của chi tiết
        });
    //-----------chức năng tìm kiếm ajax---------------
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // Tìm thùng hàng
    $(document).ready(function() {
        $("#searchContainer").on('submit', function(e) {
            e.preventDefault(); // Ngăn form gửi theo cách truyền thống
            var data = $(this).serialize();
            $.ajax({
                url: path, // Sửa lại đường dẫn phù hợp với route của bạn
                type: "POST",
                data: data, // Serialize dữ liệu form
                success: function(response) {
                    currentData = data;
                    currentType = 'searchContainer';
                    //console.log(response);
                    updateTableContent(response.search_transactions.data);//cập nhật nội dung
                    updatePagination(response.search_transactions);// Cập nhật phân trang
                },
                error: function(xhr, status, error) {
                    // Xử lý lỗi
                    console.error(error);
                }
            });//kết thúc ajax
        });//kết thúc searchContainer
    });
    //-------------------------
    function updateTableContent(data) {
        $("#myTable tbody").empty(); // Xóa nội dung hiện tại của bảng
        if (data && data.length > 0) {
            data.forEach(function(transaction) {
                var transactionDate = new Date(transaction.updated_at);
                    // Bạn có thể chỉ định 'en-US' hoặc locale khác tùy thuộc vào định dạng bạn muốn
                    var formattedDate = transactionDate.toLocaleDateString('vi-VN', {
                        year: '2-digit', // Hiển thị 4 chữ số của năm
                        month: '2-digit', // Hiển thị tháng với 2 chữ số
                        day: '2-digit'   // Hiển thị ngày với 2 chữ số
                    });
                      // Tạo một hàng mới với dữ liệu transactions
                      var newRow = `
                          <tr>
                            <td class="expand-button" data-target="#details${transaction.id}">></td>
                            <td>${formattedDate}</td>
                            <td>${transaction.container_id}</td>
                            <td>${transaction.quantity}</td>
                            <td>${transaction.type}</td>
                            <td>${transaction.inventory_history.quantity_after}</td>
                            <td>${transaction.productapi.name}</td>
                          </tr>
                          <tr id="details${transaction.id}" class="details-row" style="display: none;">
                              <td></td>
                              <td colspan="6">
                                <div><strong>Cập nhật:</strong> ${transaction.updated_at}</div>
                                <div><strong>SKU:</strong> ${transaction.productapi.sku}</div>
                                <div><strong>Loại:</strong> ${transaction.transaction_type.type_name}</div>
                                <div><strong>User:</strong> ${transaction.user.name}</div> 
                                <div><strong>Ghi chú:</strong> ${transaction.inventory_history.notes}</div> 
                              </td>
                          </tr>
                      `;
                      // Thêm hàng mới vào bảng
                      $("#myTable tbody").append(newRow);
                  });
              } else {
                  // Trường hợp không có dữ liệu trả về
                  $("#myTable tbody").append('<tr><td colspan="6">Không tìm thấy thùng hàng nào.</td></tr>');
              }
    }

    function updatePagination(paginationData) {
        var paginationContainer = $('#pagination');
        paginationContainer.empty(); // Xóa các liên kết phân trang hiện tại
        if (paginationData.last_page > 1) {
            for (let page = 1; page <= paginationData.last_page; page++) {
            // Tạo mỗi liên kết phân trang
            var li = $('<li>').addClass('page-item');
            var a = $('<a>').addClass('page-link')
                            .attr('href', '#')
                            .text(page)
                            .data('page', page); // Sử dụng data attribute để lưu trữ số trang

            // Đánh dấu liên kết hiện tại là active
            if (page === paginationData.current_page) {
                li.addClass('active');
            }
            li.append(a);
            paginationContainer.append(li);
            }

            // Sử dụng event delegation để gắn sự kiện click
            paginationContainer.on('click', '.page-link', function(e) {
            e.preventDefault();
            var selectedPage = $(this).data('page'); // Lấy số trang từ data attribute
            // Gửi yêu cầu AJAX tới server với trang được chọn
            // Ví dụ: loadPageData('/your-endpoint?page=' + selectedPage);
            });
        }
    }

    $(document).ready(function() {
        updateTableContent(transactions.data);//cập nhật data ban đầu
        updatePagination(transactions);// Cập nhật phân trang ban đầu
        $('#pagination').on('click', 'a', function(e) {
            e.preventDefault();
            // Sử dụng data attribute để lấy số trang thay vì href
            var pageNumber = $(this).data('page');
            // Xây dựng URL cho trang cụ thể dựa trên số trang
            //console.log(currentType); 
            if (currentType === 'searchContainer') {
                var pageUrl = path + '?page=' + pageNumber; // Thay đổi đường dẫn phù hợp với API của bạn
            } else if (currentType === 'transaction') {
                var pageUrl = origin_path + '?page=' + pageNumber; // Thay đổi đường dẫn phù hợp với API của bạn
            }
            loadPageData(pageUrl);
        });
    });

    function loadPageData(url) {// hàm chạy khi chọn nút chuyển trang
            $.ajax({
                url: url,
                type: "POST",
                data: currentData,
                success: function(response) {
                    let firstKey = Object.keys(response)[0]; // lấy tên object
                    //console.log(firstKey);
                    updateTableContent(response[firstKey].data);//cập nhật nội dung
                    updatePagination(response[firstKey]);// Cập nhật phân trang    
                },
                error: function(xhr, status, error) {
                    // Xử lý lỗi
                    console.error(error);
                }
            });
        }

    function refeshData() {
        currentType = 'transaction';
        currentData = transactions;
        //console.log(currentData);
        loadPageData(origin_path);
    }



// Ví dụ thêm sự kiện click cho các nút mở rộng
$('.expand-button').on('click', function() {
    var targetId = $(this).data('target');
    $(targetId).toggle(); // Hoặc bất kỳ logic nào bạn muốn thực hiện khi nút được nhấn
});


</script>
@endpush