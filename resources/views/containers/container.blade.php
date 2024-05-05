@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="container mx-auto px-2 sm:px-3 lg:px-4">
    @include('containers.add_container_modal')

    <div class="mx-auto mt-2">
        @if(auth()->user() && auth()->user()->can('edit posts'))
            <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 mb-2 rounded" onclick="openModal()">
                Thêm Thùng
            </button>
        @endif
        <form id="searchProduct" action="" method="POST" class="mb-3 bg-white p-2 sm:p-4 rounded-lg shadow-md">
            <div class="flex flex-wrap -mx-3">
                <div class="w-full sm:w-4/12 px-3 mb-2 md:mb-4">
                    <label for="search_product_id" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Nhập sản phẩm:</label>
                    <div class="relative">
                        <input type="text" id="searchProductID" class="shadow appearance-none border rounded mt-1 block w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập tên sản phẩm">
                        <input type="hidden" id="searchProductIDValue" name="searchProductID">
                        <div class="absolute inset-y-0 right-0 flex items-center px-2">
                            <button class="bg-yellow-600 text-gray-50 p-2 rounded-r-md hover:bg-yellow-800 z-40" type="button" onclick="clearSelection()">Xóa</button>
                        </div>
                    </div>
                </div>
                <div class="w-full sm:w-1/12 px-3 mb-2 md:mb-4">
                    <label for="location_id" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Vị trí:</label>
                    <select name="location_id" id="location_id" class="block appearance-none w-full bg-white border border-gray-300 hover:border-gray-400 px-4 py-2 pr-8 rounded leading-tight focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">Chọn</option>
                        @foreach($locations as $location)
                            @if($location->isParent())
                            <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-2/12 px-3 mb-2 md:mb-4">
                    <label for="branch_id" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Chi nhánh:</label>
                    <select class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" name="branch_id">
                        <option value="">Chọn chi nhánh</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-2/12 px-3 mb-2 md:mb-4">
                    <label for="container_status_id" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Trạng thái:</label>
                    <select class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" name="container_status_id">
                        <option value="">Chọn trạng thái container</option>
                        @foreach($containerStatuses as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-2/12 px-3 mb-2 md:mb-4">
                    <label for="bundle_type" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Loại SP:</label>
                    <select class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" name="bundle_type">
                        <option value="">Chọn loại bundle</option>
                        <!-- Additional options for bundle type here -->
                    </select>
                </div>
                <div class="w-full sm:w-1/12 px-3 mt-1 md:mb-0">
                    <button type="submit" class="bg-blue-500 text-white py-1 px-4 w-full rounded hover:bg-blue-600 mt-2 sm:mt-6">Tìm</button>
                </div>
            </div>
        </form>

        <form id="searchContainer" action="" method="POST" class="flex items-center bg-white p-2 sm:p-4 rounded-lg shadow space-x-4">
            <div class="flex items-center space-x-2">
                <input type="text" class="px-4 py-2 border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded transition duration-150 ease-in-out" placeholder="Nhập mã thùng" name="container_id" id="search_container_id">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-150 ease-in-out">Tìm thùng</button>
                <button type="button" class="bg-gray-200 text-black px-4 py-2 rounded hover:bg-gray-300 transition duration-150 ease-in-out" onclick="clearSearch()">Xóa</button>
            </div>
            <div class="flex items-center space-x-2 ml-2">
                <input class="form-checkbox h-5 w-5 text-blue-500" type="checkbox" id="display" name="display" value="1">
                <label class="text-gray-700" for="display">Thùng Trưng Bày</label>
            </div>
            <div class="flex items-center space-x-2 ml-2">
                <input class="form-checkbox h-5 w-5 text-blue-500" type="checkbox" id="stock" name="stock" value="1">
                <label class="text-gray-700" for="stock">Thùng Kho</label>
            </div>
            <button id="refesh_data" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition duration-150 ease-in-out">Xóa bộ lọc</button>
        </form>
    </div>

    <div class="bg-white shadow-md rounded my-1 overflow-x-auto p-2 sm:p-4">
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
    function openModal() {
        const modal = document.getElementById('addContainerModal');
        modal.classList.remove('hidden'); // Giả sử bạn đã ẩn modal với class 'hidden' của Tailwind
    }
    function closeModal() {
        const modal = document.getElementById('addContainerModal');
        modal.classList.add('hidden');
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

    function clearSelection() {
        document.getElementById('searchProductID').value = "";
        // document.getElementById('searchProductIDvalue').value = "";
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
                    containerCodeError.textContent = 'Mã thùng chưa đúng định dạng. Mã thùng phải có 6 ký tự, ký tự thứ 2 là chữ cái, còn lại là số.';
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
        // console.log(existingCodes);
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
    var products = @json($products);
    console.log(products);
    $("#productId").autocomplete({
        source: products.map(product => ({
            label: product.name,
            value: product.id,
            sku: product.sku
        })),
        select: function(event, ui) {
            console.log(ui.item);
            $('#productId').val(ui.item.label); // Set the visible input value to the label
            $('#productIDValue').val(ui.item.value); // Set the hidden input value to the ID
            return false; // Prevent the widget from automatically updating the value of the input with the selected value
        },
        focus: function(event, ui) {
            $('#productId').val(ui.item.label);
            $('#productSku').text('SKU: ' + ui.item.sku);
            return false; // Prevent the widget from replacing the input's value with the value during focus
        }
    });

    $("#searchProductID").autocomplete({
        source: products.map(product => ({
            label: product.name,
            value: product.id
        })),
        select: function(event, ui) {
            //console.log(ui.item);
            $('#searchProductID').val(ui.item.label);
            $('#searchProductIDValue').val(ui.item.value);
            return false;
        },
        // focus: function(event, ui) {
        //     $('#searchProductID').val(ui.item.label);
        //     return false;
        // }
    });

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

    function clearSearch() {
        document.getElementById('search_container_id').value = ""; // Xóa nội dung của ô nhập liệu
    }

});
    
</script>
@endpush
