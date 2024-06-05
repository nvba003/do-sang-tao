@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="container mx-auto px-2 sm:px-3 lg:px-4">
    <div id="formContainer" class="w-full mx-auto mt-2 rounded bg-white">
        <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm" class="shadow-md rounded px-2 sm:px-4 py-2 mb-1">
            @csrf
            <div class="flex flex-wrap -mx-2">
                <div class="px-2 w-full md:w-12/24 xl:w-7/24 mb-2">
                    <div class="flex flex-wrap -mx-2">
                        <div class="px-2 w-2/5">
                            <label for="container_code" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Mã Thùng</label>
                            <input type="text" class="appearance-none block w-full bg-gray-100 text-gray-700 text-sm border rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white" id="container_code" name="container_code" placeholder="Nhập mã thùng" required>
                        </div>
                        <div class="px-2 w-2/5">
                            <label for="quantity" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Số Lượng</label>
                            <div class="flex mt-1">
                                <button type="button" class="text-sm bg-gray-400 hover:bg-gray-500 text-white font-bold py-1 px-3 rounded-l focus:outline-none" onclick="decreaseQuantity()">-</button>
                                <input type="number" class="w-full text-center border-t border-b border-gray-300 py-1 focus:outline-none" id="quantity" name="quantity" value="1" min="1" step="0.1" required>
                                <button type="button" class="text-sm bg-gray-400 hover:bg-gray-500 text-white font-bold py-1 px-3 rounded-r focus:outline-none" onclick="increaseQuantity()">+</button>
                            </div>
                        </div>
                        <div class="px-2 w-1/5 mt-5">
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Lưu</button>
                        </div>
                    </div>
                    <div class="px-2 w-full">
                        <input type="hidden" id="container_id" name="container_id">
                        <input type="hidden" id="product_id" name="product_id">
                        <div id="product_info" class="text-sm"></div>
                    </div>
                </div>
                <div class="px-2 w-full md:w-12/24 xl:w-5/24 mb-2">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Lựa chọn</label>
                    <div class="flex">
                        <label class="flex-1 text-sm bg-blue-500 hover:bg-blue-700 text-white py-2 px-1 rounded-l focus:outline-none">
                            <input type="radio" name="type" id="type_in" value="1" autocomplete="off"> Nhập
                        </label>
                        <label class="flex-1 text-sm bg-red-500 hover:bg-red-700 text-white py-2 px-1 focus:outline-none">
                            <input type="radio" name="type" id="type_out" value="2" autocomplete="off"> Xuất
                        </label>
                        <label class="flex-1 text-sm bg-green-500 hover:bg-green-700 text-white py-2 px-1 rounded-r focus:outline-none">
                            <input type="radio" name="type" id="type_check" value="3" autocomplete="off"> Kiểm
                        </label>
                    </div>
                </div>
                <div class="px-2 w-1/2 md:w-6/24 xl:w-3/24 mb-1">
                    <label for="branch_id" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Chi Nhánh</label>
                    <select class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="branch_id" name="branch_id" required>
                        <option value="">Chọn chi nhánh</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="px-2 w-1/2 md:w-6/24 xl:w-3/24 mb-1">
                    <label for="transaction_type_id" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Loại</label>
                    <select class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="transaction_type_id" name="transaction_type_id" required>
                        <option value="">Chọn loại</option>
                        @foreach($transactionTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="px-2 w-full md:w-12/24 xl:w-5/24 mb-1">
                    <label for="notes" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Ghi Chú</label>
                    <textarea class="appearance-none block w-full bg-gray-100 text-gray-700 text-sm border border-gray-200 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="notes" name="notes" rows="1"></textarea>
                </div>
            </div>
        </form>
    </div>

    <div class="flex flex-wrap mx-auto p-2 bg-white rounded shadow-md mb-2">
        <div class="w-full md:w-1/2 lg:w-2/5 px-2 py-1 text-sm">
            <form id="searchProduct" action="" method="POST" class="mb-1">
                @csrf
                <div class="flex justify-between items-center">
                    <div class="flex-grow pr-2">
                        <label for="search_product_id" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Tìm sản phẩm:</label>
                        <div class="relative">
                            <input type="text" id="searchProductID" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập tên sản phẩm">
                            <input type="hidden" id="searchProductIDValue" name="searchProductID">
                            <div class="absolute inset-y-0 right-0 flex items-center px-2">
                                <button class="bg-gray-200 hover:bg-gray-300 text-gray-500 p-2 rounded-r-md" type="button" onclick="clearSearchProduct()">Xóa</button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 mt-5">Tìm</button>
                </div>
            </form>
        </div>
        <div class="w-full md:w-1/2 lg:w-2/5 px-2 py-1 text-sm">
            <form id="searchContainer" action="" method="POST" class="mb-1">
                @csrf
                <div class="flex justify-between items-center">
                    <div class="flex-grow pr-2">
                        <div class="relative">
                            <label for="search_container_code" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Tìm thùng:</label>
                            <input type="text" class="px-2 py-2 border text-sm border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded transition duration-150 ease-in-out w-full" placeholder="Nhập mã thùng" name="search_container_code" id="search_container_code">
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 mt-5">
                                <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-500 p-2 rounded-r-md" onclick="clearSearchContainer()">Xóa</button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-5">Tìm</button>
                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 ml-2 mt-5" onclick="clearForm()">Xóa lọc</button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg my-1 overflow-x-auto p-2 sm:p-4">
        <table class="w-full leading-normal" id="transactionTable">
            <thead class="text-white bg-gray-500">
                <tr>
                    <th scope="col" class="w-1/24 px-2 py-3 mt-1 text-center text-xs md:text-sm hidden sm:block font-semibold uppercase tracking-wider">
                        <input type="checkbox" id="checkAll">
                    </th>
                    <th scope="col" class="w-1/24 px-2 py-3 text-center text-xs md:text-sm font-semibold uppercase tracking-wider"></th>
                    <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Ngày</th>
                    <th scope="col" class="w-2/24 px-2 py-3 text-center text-xs md:text-sm font-semibold uppercase tracking-wider">ID</th>
                    <th scope="col" class="w-2/24 px-2 py-3 text-center text-left text-xs md:text-sm font-semibold uppercase tracking-wider">SL</th>
                    <th scope="col" class="w-3/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Loại</th>
                    <th scope="col" class="w-2/24 px-2 py-3 text-center text-xs md:text-sm font-semibold uppercase tracking-wider">Tồn</th>
                    <th scope="col" class="w-10/24 px-2 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Tên sản phẩm</th>
                </tr>
            </thead>
            <tbody>
                @include('containers.partial_transaction_table', ['transactions' => $transactions])
            </tbody>
        </table>
    </div>
    <button onclick="playBeepSound()">Test Beep</button>
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
                        <option value="10">10</option>
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

@endsection

@push('scripts')
<script>
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
    function playBeepSound() {
        var audio = document.getElementById('beep-sound');
        audio.play();
    }
    window.onload = function() {
        @if(session('success'))
            playBeepSound();
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
    function updateBackground(type) {
        var container = document.getElementById('formContainer');
        container.classList.remove('bg-white', 'bg-blue-100', 'bg-red-100', 'bg-green-100'); // Xóa các class cũ
        if(type === 'Nhap') {
            container.classList.add('bg-blue-100'); // Thêm class mới
        } else if(type === 'Xuat') {
            container.classList.add('bg-red-100');
        } else if(type === 'Kiem') {
            container.classList.add('bg-green-100');
        }
    }
    // Gắn hàm updateBackground vào sự kiện click của từng nút
    document.getElementById('type_in').addEventListener('click', function() { updateBackground('Nhap'); });
    document.getElementById('type_out').addEventListener('click', function() { updateBackground('Xuat'); });
    document.getElementById('type_check').addEventListener('click', function() { updateBackground('Kiem'); });

    document.addEventListener('DOMContentLoaded', function() {
        var input = document.getElementById('container_code');
        var productInfo = document.getElementById('product_info');
        input.addEventListener('change', function(event) {
            var containerCode = this.value; // Lấy giá trị từ input
            if (containerCode) {
                // Sử dụng phương thức POST để gửi dữ liệu
                fetch('{{ route("transactions.fetchProduct") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        container_code: containerCode  // Đóng gói dữ liệu container_code vào JSON
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hiển thị thông tin sản phẩm
                        console.log(data);
                        document.getElementById('container_id').value = data.container_id;
                        document.getElementById('product_id').value = data.product_id;
                        var branch = document.getElementById('branch_id');
                        branch.value = data.branch_id;
                        branch.classList.add('pointer-events-none');
                        productInfo.innerHTML = '<span class="text-green-500">' + data.productName + '</span><br/>' + '<span class="text-blue-500">SL Sapo: ' + data.sapo_quantity + ' || SL trong thùng: ' + data.quantity + '</span>';
                    } else {
                        document.getElementById('branch_id').classList.remove('pointer-events-none');
                        productInfo.innerHTML = '<span class="text-red-500">Mã thùng không tồn tại.</span>';
                    }
                })
                .catch(error => {
                    productInfo.textContent = 'Có lỗi xảy ra: ' + error.message;
                });
            } else {
                productInfo.textContent = 'Vui lòng nhập mã thùng.';
            }
        });
    });

    function clearSearchProduct() {
        document.getElementById('searchProductID').value = "";
        document.getElementById('searchProductIDValue').value = "";
    }
    function clearSearchContainer() {
        document.getElementById('search_container_code').value = "";
    }
    function clearForm() {
        event.preventDefault();
        clearSearchProduct();
        clearSearchContainer();
    }
    // $.ajaxSetup({
    //     headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     }
    // });
    $(document).ready(function() {
        let currentSearchParams = "";
        let currentPerPage = "";
        let perPage = $('#perPage').val();
        var transactions = @json($transactions)['data'];
        console.log(transactions);
        var products = @json($products);
        // console.log(products);
  
        $("#searchProductID").autocomplete({
            source: products.map(product => ({
                label: product.sku + " - " + product.name,
                value: product.id
            })),
            select: function(event, ui) {
                //console.log(ui.item);
                $('#searchProductID').val(ui.item.label);
                $('#searchProductIDValue').val(ui.item.value);
                return false;
            },
        });

        function fetchData(url) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#transactionTable tbody').html(response.table);
                    $('#pagination-links').html(response.links);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        fetchData('{{ route('transactions.show') }}');

        $('#searchProduct').on('submit', function(e) {
            e.preventDefault();
            perPage = $('#perPage').val();
            currentSearchParams = updateSearchParams('per_page', perPage, $(this).serialize());
            fetchData('{{ route('transactions.show') }}?' + currentSearchParams);
        });

        $('#searchContainer').on('submit', function(e) {
            e.preventDefault();
            perPage = $('#perPage').val();
            currentSearchParams = updateSearchParams('per_page', perPage, $(this).serialize());
            fetchData('{{ route('transactions.show') }}?' + currentSearchParams);
        });

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
            fetchData('{{ route('transactions.show') }}?' + currentSearchParams);
        });
        function updateSearchParams(key, value, paramsString) {
            var searchParams = new URLSearchParams(paramsString);
            searchParams.set(key, value);
            return searchParams.toString();
        }

        $('#transactionTable').on('click', '.expand-button', function() {
            var targetId = $(this).data('target');
            $(targetId).toggle();
            // Thay đổi nút từ "+" sang "-" và ngược lại
            $(this).text($(this).text() === '-' ? '+' : '-');
        });

        function updateCount() {
            var count = $('.checkItem:checked').length;
            $('#selectedCount').text(count);
        }
        $(document).on('click', '.checkItem', function() {
            updateCount();
        });
        updateCount();  

        // $('#transactionTable').on('click', '.btn-edit', function() {
        //     var promotion = $(this).data('promotion');
        //     openEditForm(promotion);
        // });

        $("#searchContainer").on('submit', function(e) { //tìm mã thùng, form tự gửi data đến server
            e.preventDefault(); // Ngăn form gửi theo cách truyền thống
            clearSearchProduct(); // Tìm thùng thì xóa tìm Sản phẩm
        });//kết thúc searchContainer

        $("#searchProduct").on('submit', function(e) {//tìm sản phẩm, form tự gửi data đến server
            e.preventDefault(); // Ngăn form gửi theo cách truyền thống
            clearSearchContainer(); // Tìm sản phẩm thì xóa tìm mã thùng
        });//kết thúc searchProduct
        
        // $('#transactionForm').on('submit', function(e) {
        //     e.preventDefault(); // Ngăn chặn hành vi gửi form mặc định
        //     var formData = $(this).serialize(); // Lấy dữ liệu từ form
        //     $.ajax({
        //         type: 'POST',
        //         url: '{{ route('transactions.store') }}',
        //         data: formData,
        //         success: function(response) {
        //             if(response.success) {
        //                 playBeepSound();
        //                 //addNewTransactionRow(response.transaction); // Hàm để thêm hàng mới
        //                 alert('Giao dịch được cập nhật thành công!');
        //             } else {
        //                 alert('Có lỗi xảy ra, vui lòng thử lại.');
        //             }
        //         },
        //         error: function(error) {
        //             console.error('Error:', error);
        //             alert('Đã xảy ra lỗi, vui lòng thử lại.');
        //         }
        //     });
        // });
        

    });


</script>
@endpush