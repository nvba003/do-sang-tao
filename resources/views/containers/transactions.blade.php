@extends('layouts.app')
@php
use Carbon\Carbon;
@endphp

@section('content')
<div id="formContainer" class="container mt-3">
    <!--<h4 class="mb-4">Nhập/Xuất Thùng Hàng</h4>-->
    <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
        @csrf

        <div class="row">
            <!-- Gộp Chọn Chi Nhánh và Loại Giao Dịch -->
            <div class="form-group col-md-6 mb-1">
                <label for="branch_id">Chọn Chi Nhánh</label>
                <select class="form-control form-control-sm" id="branch_id" name="branch_id" required>
                    <option value="">Chọn chi nhánh</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6 mb-1">
                <label>Lựa chọn</label>
                <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                    <label class="btn btn-primary btn-sm flex-fill">
                        <input type="radio" name="type" id="type_in" value="Nhap" autocomplete="off" checked> Nhập
                    </label>
                    <label class="btn btn-danger btn-sm flex-fill">
                        <input type="radio" name="type" id="type_out" value="Xuat" autocomplete="off"> Xuất
                    </label>
                    <label class="btn btn-success btn-sm flex-fill">
                        <input type="radio" name="type" id="type_check" value="Kiem" autocomplete="off"> Kiểm
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Gộp Mã Thùng và TransactionTypes -->
            <div class="form-group col-md-6 mb-1">
                <label for="container_id">Mã Thùng</label>
                <input type="text" class="form-control form-control-sm" id="container_id" name="container_id" placeholder="Nhập mã thùng" required>
            </div>
            <div class="form-group col-md-6 mb-1">
                <label for="transaction_type_id">Loại Giao Dịch</label>
                <select class="form-control form-control-sm" id="transaction_type_id" name="transaction_type_id" required>
                    <option value="">Chọn loại</option>
                    @foreach($transactionTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Trường ẩn để lưu thông tin sản phẩm -->
        <input type="hidden" id="product_id" name="product_info" value="">
        <!-- Khu vực hiển thị thông tin sản phẩm -->
        <div id="product_info" class="mt-0 mb-0 text-success">
            <!-- Thông tin sản phẩm sẽ được hiển thị ở đây thông qua JavaScript -->
        </div>
        <!-- Khu vực hiển thị thông báo lỗi -->
        <div id="error_message" class="mt-0 text-danger mb-0">
            <!-- Thông báo lỗi sẽ được hiển thị ở đây thông qua JavaScript -->
        </div>

        <div class="row">
            <!-- Gộp Số Lượng và Ghi Chú -->
            <div class="form-group col-md-6 mb-2">
                <label for="quantity">Số Lượng</label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <button type="button" class="btn btn-dark wide-button" onclick="decreaseQuantity()">-</button>
                    </div>
                    <input type="number" class="form-control text-center" id="quantity" name="quantity" value="1" min="1" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-dark wide-button" onclick="increaseQuantity()">+</button>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-6 mb-2">
                <label for="notes">Ghi Chú</label>
                <textarea class="form-control form-control-sm" id="notes" name="notes" rows="1"></textarea>
            </div>
        </div>

        <!-- Làm nhỏ nút Thực Hiện -->
        <button type="submit" class="btn btn-warning btn-sm mb-2 mt-2">Lưu giao dịch</button>
        
    </form>
</div>

<!-- tìm kiếm giao dịch thùng hàng-->    
<div id="searchForm" class="container mt-3">
    <form id="searchContainer" action="" method="POST" class="mb-3">
        @csrf
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Nhập mã thùng" name="container_id" id="search_container_id">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Tìm Kiếm</button>
                <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">Xóa Tìm Kiếm</button>
            </div>
        </div>
    </form>
</div>

<!-- Hiển thị dữ liệu trong table -->    
<div class="container mt-4">
    <!-- Phần chứa phân trang -->
    <nav aria-label="Page navigation example" id="paginationContainer">
        <ul class="pagination" id="pagination">
            <!-- Các liên kết phân trang sẽ được thêm vào đây -->
        </ul>
    </nav>
    <div class="row">
        <div class="col-md-12">
            <table id="myTable" class="table">
                <thead>
                    <tr>
                      <th></th> <!-- Cột cho dấu "+" -->  
                      <th scope="col">Ngày</th>
                      <th scope="col">ID thùng</th>
                      <th scope="col">Số Lượng</th>
                      <th scope="col">Loại</th>
                      <th scope="col">Tồn</th>
                      <th scope="col">Tên Sản Phẩm</th>
                    </tr>
                </thead>
                <tbody>
                    <!--@forelse ($transactions as $transaction)
                    <tr>
                      <td class="expand-button" data-target="#details{{ $transaction->id }}">></td>
                      <td>{{ Carbon::parse($transaction->updated_at)->format('Y-m-d') }}</td>
                      <td>{{ $transaction->container_id}}</td>
                      <td>{{ $transaction->quantity }}</td>
                      <td>{{ $transaction->type }}</td>
                      <td>{{ $transaction->inventoryHistory->quantity_after }}</td>
                      <td>{{ $transaction->productapi->name }}</td>
                      
                    </tr>

                    <tr id="details{{ $transaction->id }}" class="details-row">
                        <td></td>
                        <td colspan="6">
                            <div><strong>Cập nhật:</strong> {{ $transaction->updated_at }}</div>
                            <div><strong>SKU:</strong> {{ $transaction->productapi->sku }}</div>
                            <div><strong>Loại:</strong> {{ $transaction->transactionType->type_name }}</div>
                            <div><strong>User:</strong> {{ $transaction->user->name }}</div>
                            <div><strong>Ghi chú:</strong> {{ $transaction->inventoryHistory->notes }}</div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="3">Không tìm thấy thùng hàng nào.</td>
                        </tr>
                    @endforelse
                    -->
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