@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="container mx-auto px-2 sm:px-3 lg:px-4">
    <!-- Modal thêm thùng hàng -->
    <div class="fixed bg-gray-600 bg-opacity-0 z-50 overflow-y-auto h-full w-full hidden" id="addContainerModal">
        <div class="relative top-10 mx-auto p-5 border w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="addContainerModalLabel">Thêm Thùng Hàng Mới</h3>
                    <button type="button" class="bg-transparent hover:bg-gray-200 text-gray-500 font-semibold py-2 px-2 rounded inline-flex items-center" onclick="closeModal()">
                    <!-- <svg class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg> -->
                    </button>
                </div>
                <!-- Form -->
                <form action="{{ route('containers.store') }}" method="POST" id="addContainerForm" class="mt-2">
                    @csrf
                    <div class="px-4 py-5 bg-white sm:p-6">
                        <div class="grid grid-cols-3 gap-6">
                            <div class="col-span-3 sm:col-span-2">
                            <label for="containerId" class="block text-sm font-medium text-gray-700">Mã thùng:</label>
                            <input type="text" name="id" id="containerId" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Nhập mã thùng">
                            <div id="containerCodeError" class="text-xs text-red-500 mt-1 hidden">Mã thùng đã tồn tại.</div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="productId" class="block text-sm font-medium text-gray-700">Sản Phẩm:</label>
                            <select id="productId" name="productId" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Chọn Sản Phẩm</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="unit" class="block text-sm font-medium text-gray-700">Đơn Vị:</label>
                            <input type="text" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="unit" name="unit" placeholder="Nhập đơn vị">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="branchId" class="block text-sm font-medium text-gray-700">Chi Nhánh:</label>
                            <select id="branchId" name="branch_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Chọn chi nhánh</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option> 
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="parent_menu" class="block text-sm font-medium text-gray-700">Menu Cha:</label>
                            <select name="parent_id" id="parent_menu" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Chọn Menu Cha (Nếu có)</option>
                                @foreach($containerMenuOptions as $menuOption)
                                    @if($menuOption->isParent())
                                        <option value="{{ $menuOption->id }}" data-parent-menu="{{ $menuOption->definition_id }}">{{ $menuOption->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="child_menu" class="block text-sm font-medium text-gray-700">Menu Con:</label>
                            <select name="child_id" id="child_menu" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Chọn Menu Con (Dựa trên Menu Cha)</option>
                            </select>
                        </div>

                        <div class="flex items-center mb-4">
                            <input type="checkbox" name="display_box" id="display_box" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="display_box" class="ml-2 block text-sm text-gray-900">
                                Tạo Trưng Bày
                            </label>
                        </div>

                        <div class="flex items-center mb-4">
                            <input type="checkbox" name="branch_box" id="branch_box" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="branch_box" class="ml-2 block text-sm text-gray-900">
                                Tạo 2 chi nhánh
                            </label>
                        </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded" onclick="closeModal()">
                        Đóng
                        </button>
                        <button type="submit" class="ml-3 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Lưu
                        </button>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mx-auto mt-3">
        @if(auth()->user() && auth()->user()->can('edit posts'))
            <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="openModal()">
                Thêm Thùng
            </button>
        @endif
        <form id="searchProduct" action="" method="POST" class="mb-3">
            <div class="flex flex-wrap">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label for="search_product_id" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Nhập sản phẩm:</label>
                    <div class="relative">
                        <select name="search_product_id" id="search_product_id" class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline" data-live-search="true">
                            <option value="">Chọn Sản Phẩm</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <button class="bg-gray-200 text-black p-2 rounded-r-md hover:bg-gray-300" type="button" onclick="clearSelection()">Xóa</button>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label for="location_id" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Lọc vị trí:</label>
                    <select name="location_id" id="location_id" class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline" data-live-search="true">
                        <option value="">Chọn vị trí</option>
                        @foreach($locations as $location)
                            @if($location->isParent())
                                <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label for="child_location_id" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Lọc thùng số:</label>
                    <select name="child_location_id" id="child_location_id" class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline" data-live-search="true">
                        <option value="">Chọn thùng số</option>
                        <!-- Dynamic options will be here -->
                    </select>
                </div>
                <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 w-full rounded hover:bg-blue-600 mt-6">Tìm sản phẩm</button>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                        <select class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="branch_id">
                            <option value="">Chọn chi nhánh</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                        <select class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="container_status_id">
                            <option value="">Chọn trạng thái container</option>
                            @foreach($containerStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                        <select class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="bundle_type">
                            <option value="">Chọn loại bundle</option>
                            <!-- Additional options for bundle type here -->
                        </select>
                    </div>
                </div>

            </div>
        </form>

        <form id="searchContainer" action="" method="POST" class="flex items-center">
            <div class="flex items-center mr-2">
                <input type="text" class="form-control px-4 py-2 border rounded" placeholder="Nhập mã thùng" name="container_id" id="search_container_id">
                <button type="submit" class="ml-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Tìm thùng</button>
                <button type="button" class="ml-2 bg-gray-200 text-black px-4 py-2 rounded hover:bg-gray-300" onclick="clearSearch()">Xóa</button>
            </div>
            <div class="flex items-center mx-2">
                <input class="form-checkbox h-5 w-5" type="checkbox" id="display" name="display" value="1">
                <label class="ml-2 text-gray-700" for="display">Thùng Trưng Bày</label>
            </div>
            <div class="flex items-center mx-2">
                <input class="form-checkbox h-5 w-5" type="checkbox" id="stock" name="stock" value="1">
                <label class="ml-2 text-gray-700" for="stock">Thùng Kho</label>
            </div>
            <button id="refesh_data" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 ml-2">Xóa bộ lọc</button>
        </form>
    </div>

    <div class="bg-white shadow-md rounded my-1 overflow-x-auto">
        <table class="min-w-full leading-normal" id="containerTable">
            <thead class="text-white bg-gray-500">
                <tr>
                    <th scope="col" class="px-2 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase tracking-wider">
                        <input type="checkbox" id="checkAll">
                    </th>
                    <th scope="col" class="px-2 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase tracking-wider"></th>
                    <th scope="col" class="px-3 py-3 border-b-2 border-gray-200 text-center text-sm font-semibold uppercase tracking-wider">
                        ID thùng
                    </th>
                    <th scope="col" class="px-3 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase tracking-wider">
                        Số lượng
                    </th>
                    <th scope="col" class="px-3 py-3 border-b-2 border-gray-200 text-right text-sm font-semibold uppercase tracking-wider">
                        Vị trí
                    </th>
                    <th scope="col" class="px-3 py-3 border-b-2 border-gray-200 text-right text-sm font-semibold uppercase tracking-wider">
                        Thùng số
                    </th>
                    <th scope="col" class="px-3 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase tracking-wider">
                        Tên sản phẩm
                    </th>
                </tr>
            </thead>
            <tbody>
                @include('containers.partial_container_table', ['containers' => $containers])
            </tbody>
        </table>
    </div>
    <div class="grid grid-cols-10 gap-4 m-2">
        <div class="col-span-8" id="pagination-links">
            <!-- Pagination links here -->
        </div>
        <div class="col-span-2">
            <div class="flex items-center space-x-2 w-full">
                <label for="perPage" class="text-sm flex-1 text-right pr-2">Số hàng:</label>
                <select id="perPage" class="form-control form-control-sm text-sm flex-1">
                    <option value="20">20</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@endsection



@push('scripts')
<script>
    function openModal() {
        const modal = document.getElementById('addContainerModal');
        modal.classList.remove('hidden'); // Giả sử bạn đã ẩn modal với class 'hidden' của Tailwind
    }
    function closeModal() {
        const modal = document.getElementById('addContainerModal');
        modal.classList.add('hidden');
    }
    function toggleModal(show) {
            const modal = document.getElementById('modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

    var containers = @json($containers);
    var currentData = containers; // Biến toàn cục giữ dữ liệu hiện tại
    var currentType = 'container';
    var origin_path = "{{ route('containers.data') }}"; // Định nghĩa URL cho AJAX request
    var path = "{{ route('containers.searchProduct') }}"; // Định nghĩa URL cho AJAX request

    //===============CHỨC NĂNG TẠO MÃ THÙNG=============
    var existingCodes = @json($existingCodes);
    document.addEventListener('DOMContentLoaded', function() {
        // Thêm event listener cho dropdown menu cha, khi chọn menu cha sẽ hiện menu con tương ứng
        var parentMenuSelect = document.getElementById('parent_menu');
        if (parentMenuSelect) {
            parentMenuSelect.addEventListener('change', function() {
                let parentId = this.value;
                let childMenuSelect = document.getElementById('child_menu');
                childMenuSelect.innerHTML = '<option value="">Chọn Menu Con (Dựa trên Menu Cha)</option>';
                if (parentId) {
                    // Gửi yêu cầu đến server để lấy menu con dựa trên parentId
                    fetch('api/container-menu-options/children/' + parentId)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(function(child) {
                                let option = new Option(child.name, child.id);
                                option.setAttribute('data-child-menu', child.definition_id); // lấy thuộc tính definition_id
                                childMenuSelect.add(option);
                            });
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        }

    // ---Kiểm tra điều kiện trước khi thêm thùng------
    var addContainerForm = document.getElementById('addContainerForm');
    if (addContainerForm) {
      addContainerForm.addEventListener('submit', function(event) {
          event.preventDefault(); // Ngăn form được gửi đi mặc định

          var containerId = document.getElementById('containerId').value;
          var containerCodeError = document.getElementById('containerCodeError');

          // Kiểm tra định dạng mã thùng
          var isValidCode = /^[0-9][A-Za-z][0-9]{4}$/.test(containerId);

          if (!isValidCode || containerId.length !== 6) {
              // Nếu mã thùng không đúng, hiển thị thông báo lỗi và ngăn không cho form submit
              containerCodeError.style.display = 'block';
              containerCodeError.textContent = 'Mã thùng chưa đúng định dạng. Mã thùng phải có 6 ký tự, ký tự thứ 2 là chữ cái, các ký tự còn lại là số.';
          } else {
              // Nếu mã thùng đúng, ẩn thông báo lỗi (nếu có) và cho phép form submit
              containerCodeError.style.display = 'none';
              // Tiến hành gửi form nếu cần
              addContainerForm.submit(); // Mở dòng này nếu bạn muốn gửi form sau khi tất cả các kiểm tra đã hoàn tất
          }
      });
    }
  //-------------Gợi ý mã thùng từ các lựa chọn----------------------------
        // Lắng nghe sự kiện change trên các dropdown
        document.getElementById('branchId').addEventListener('change', function() { generateContainerCode(this); });
        document.getElementById('parent_menu').addEventListener('change', function() { generateContainerCode(this); });
        document.getElementById('child_menu').addEventListener('change', function() { generateContainerCode(this); });
        document.getElementById('display_box').addEventListener('change', generateContainerCode);
        document.getElementById('refesh_data').addEventListener('click', refeshData);// Thêm sự kiện 'click' vào nút bấm
        //var existingCodes = @json($existingCodes);

        function generateContainerCode(element) {
            // Xác định dropdown nào đã kích hoạt sự kiện
            var dropdownId = element.id;
            
            // Biến để lưu trữ giá trị từ các dropdown
            var branchCode = '', parentMenuCode = '', childMenuCode = '';

            // Xử lý tùy thuộc vào dropdown được chọn
            if (dropdownId == 'branchId') {
                branchCode = element.value;
            } else if (dropdownId == 'parent_menu') {
                parentMenuCode = element.options[element.selectedIndex].getAttribute('data-parent-menu');
            } else if (dropdownId == 'child_menu') {
                childMenuCode = element.options[element.selectedIndex].getAttribute('data-child-menu');
            }

            // Lấy giá trị từ các dropdown còn lại nếu cần
            if (!branchCode) branchCode = document.getElementById('branchId').value;
            if (!parentMenuCode) parentMenuCode = document.getElementById('parent_menu').options[document.getElementById('parent_menu').selectedIndex].getAttribute('data-parent-menu');
            if (!childMenuCode) {
              // Lấy child.definition_id từ option đã chọn trong child_menu
              var childMenuSelect = document.getElementById('child_menu');
              var childMenuOption = childMenuSelect.options[childMenuSelect.selectedIndex];
              var childMenuCode = childMenuOption ? childMenuOption.getAttribute('data-child-menu') : '';
            }

            var prefix = branchCode + parentMenuCode + childMenuCode;
            // Xử lý mảng existingCodes ở đây để xác định nextNumber...
            var filteredCodes = existingCodes.filter(function(code) {
                return String(code).startsWith(prefix);
            }).map(function(code) {
                return String(code).substring(3, 5); // Lấy ký tự thứ 4 và 5
            });

            for (let i = 1; i <= 99; i++) {
                let numStr = i.toString().padStart(2, '0');
                if (!filteredCodes.includes(numStr)) {
                    nextNumber = numStr;
                    break;
                }
            }

            var isDisplayBoxChecked = document.getElementById('display_box').checked;
            var lastDigit = isDisplayBoxChecked ? '0' : ''; // Sử dụng '0' nếu display_box được chọn, ngược lại sử dụng '1' hoặc logic phức tạp hơn để xác định số cuối

            var containerCode = prefix + nextNumber + lastDigit;
            // Cập nhật mã thùng gợi ý vào trường input
            document.getElementById('containerId').value = containerCode; 
            checkexist();

            
        }
    });
  //-------------kiểm tra thùng tồn tại chưa mỗi khi thay đổi---------------------
    document.getElementById('containerId').addEventListener('input', function() {
      checkexist();
    });

    function checkexist() {// Kiểm tra xem mã thùng đã tồn tại chưa
        var existingCodes = @json($existingCodes);
        //console.log(existingCodes);
        // Lấy phần mã từ ký tự thứ 2 đến 5
        containerCode =  document.getElementById('containerId').value;
        var codeToCheck = containerCode.substring(1, 5);
        //console.log(codeToCheck);
        var idExist = '';
        var isExisting = existingCodes.some(function(code) {
            //console.log(code);
            idExist = code;
            return String(code).substring(1, 5) === codeToCheck;
        });
        // Hiển thị hoặc ẩn thông báo dựa trên kết quả kiểm tra
        var containerCodeError = document.getElementById('containerCodeError');
        if (isExisting) {
        containerCodeError.style.display = 'block';
        containerCodeError.textContent = 'Mã thùng đã tồn tại: ' + idExist;
        } else {
        containerCodeError.style.display = 'none';
        }
    }
    //===========================================================================================

    $(document).ready(function() {
    // Xử lý form tìm kiếm
    let currentSearchParams = "";
    let currentPerPage = "";
    let perPage = $('#perPage').val();
    var containers = @json($containers)['data'];
    console.log(containers);
    // var products = @json($products);
    //console.log(products);

    function fetchData(url) {
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $('#containerTable tbody').html(response.table);
                $('#pagination-links').html(response.links);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    fetchData('{{ route('containers.show') }}');

    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        perPage = $('#perPage').val();
        currentSearchParams = updateSearchParams('per_page', perPage, $(this).serialize());
        fetchData('{{ route('containers.show') }}?' + currentSearchParams);
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
        fetchData('{{ route('containers.show') }}?' + currentSearchParams);
    });
    function updateSearchParams(key, value, paramsString) {
        var searchParams = new URLSearchParams(paramsString);
        searchParams.set(key, value);
        return searchParams.toString();
    }

    $('#containerTable').on('click', '.expand-button', function() {
        var targetId = $(this).data('target');
        $(targetId).toggle();
        // Thay đổi nút từ "+" sang "-" và ngược lại
        $(this).text($(this).text() === '+' ? '-' : '+');
    });

    function notify500(){
        $('#successModal').modal('show');
        setTimeout(function() {
            $('#successModal').modal('hide');
        }, 500);
    }

    function updateCount() {
        var count = $('.checkItem:checked').length;
        $('#selectedCount').text(count);
    }
    $(document).on('click', '.checkItem', function() {
        updateCount();
    });
    updateCount();  

    $('#containerTable').on('click', '.btn-edit', function() {
        var promotion = $(this).data('promotion');
        openEditForm(promotion);
    });

    $("#searchContainer").on('submit', function(e) { //tìm mã thùng
        e.preventDefault(); // Ngăn form gửi theo cách truyền thống
        $.ajax({
            url: "{{ route('containers.searchContainer') }}", // Sửa lại đường dẫn phù hợp với route của bạn
            type: "POST",
            data: $(this).serialize(), // Serialize dữ liệu form
            success: function(response) {
                //console.log(response);
                updateTableContent(response.search_containers.data);//cập nhật nội dung
                updatePagination(response.search_containers);// Cập nhật phân trang
            },
            error: function(xhr, status, error) {
                // Xử lý lỗi
                console.error(error);
            }
        });//kết thúc ajax
    });//kết thúc searchContainer

    $("#searchProduct").on('submit', function(e) {//tìm sản phẩm
        e.preventDefault(); // Ngăn form gửi theo cách truyền thống
        var data = $(this).serialize();
        $.ajax({
            url: path, // Sửa lại đường dẫn phù hợp với route của bạn
            type: "POST",
            data: data, // Serialize dữ liệu form
            success: function(response) {
                currentData = data;
                currentType = 'searchProduct';
                //console.log(currentData);
                updateTableContent(response.search_product_containers.data);//cập nhật nội dung
                updatePagination(response.search_product_containers);// Cập nhật phân trang
            },
            error: function(xhr, status, error) {
                // Xử lý lỗi
                console.error(error);
            }
        });//kết thúc ajax
    });//kết thúc searchProduct

    $('.selectpicker').on('shown.bs.select', function () {//chức năng ẩn hiện lựa chọn
        $(this).selectpicker('refresh');
    });
    function clearSelection() {// Xóa sản phẩm đã chọn
        document.getElementById('search_product_id').value = ""; // Thiết lập giá trị của select về giá trị mặc định
        $('.selectpicker').selectpicker('refresh'); // Refresh selectpicker để cập nhật giao diện, nếu bạn đang sử dụng Bootstrap Select
    }
    function clearSearch() {
        document.getElementById('search_container_id').value = ""; // Xóa nội dung của ô nhập liệu
    }

});
    
</script>
@endpush
