@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="container">
    @if(auth()->user())
        <p><strong>Vai Trò:</strong>
            @forelse (auth()->user()->getRoleNames() as $roleName)
                <span class="badge bg-primary">{{ $roleName }}</span>
            @empty
                <span>Không có vai trò</span>
            @endforelse
        </p>
        <p><strong>Quyền:</strong>
            @php
                $permissions = auth()->user()->getAllPermissions()->pluck('name');
            @endphp

            @forelse ($permissions as $permissionName)
                <span class="badge bg-success">{{ $permissionName }}</span>
            @empty
                <span>Không có quyền</span>
            @endforelse
        </p>
    @endif
    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Click me!
            </button>
@if(auth()->user() && auth()->user()->can('edit posts'))
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addContainerModal">
    Thêm Thùng
    </button>
@endif

<!-- Button trigger modal -->
<button class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700" onclick="toggleModal(true)">Open Modal</button>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden justify-center items-center">
    <div class="bg-white rounded shadow-lg w-1/3">
        <div class="border-b px-4 py-2 flex justify-between items-center">
            <h3 class="font-semibold text-lg">Modal Title</h3>
            <button class="text-black" onclick="toggleModal(false)">
                <span class="text-xl">&times;</span>
            </button>
        </div>
        <div class="p-4">
            <p>This is some modal content. It can be anything you like.</p>
        </div>
        <div class="flex justify-end items-center p-4 border-t">
            <button class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700 mr-2" onclick="toggleModal(false)">Close</button>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="addContainerModal" tabindex="-1" aria-labelledby="addContainerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addContainerModalLabel">Thêm Thùng Hàng Mới</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ route('containers.store') }}" method="POST" id="addContainerForm">
          @csrf
          <div class="form-group">
            <label for="containerName">Mã thùng:</label>
            <input type="text" class="form-control" id="containerId" name="id" placeholder="Nhập mã thùng">
            <div id="containerCodeError" class="text-danger" style="display: none;">Mã thùng đã tồn tại.</div>
          </div>
          <!-- Sản Phẩm -->
            <div class="form-group">
                <label for="productId">Sản Phẩm:</label>
                <select name="productId" id="productId" class="form-control selectpicker" data-live-search="true">
                    <option value="">Chọn Sản Phẩm</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option> <!-- Giả sử 'name' là tên sản phẩm trong bảng productApi -->
                    @endforeach
                </select>
            </div>

            <!-- Đơn Vị -->
            <div class="form-group">
                <label for="unit">Đơn Vị:</label>
                <input type="text" class="form-control" id="unit" name="unit" placeholder="Nhập đơn vị">
            </div>

            <!-- Chi Nhánh -->
            <div class="form-group">
                <label for="branchId">Chi Nhánh:</label>
                <select class="form-control" id="branchId" name="branch_id">
                    <option value="">Chọn chi nhánh</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option> <!-- Giả sử 'name' là tên chi nhánh trong bảng branches -->
                    @endforeach
                </select>
            </div>

          <!-- Menu Cha Dropdown -->
          <div class="form-group">
            <label for="parent_menu">Menu Cha:</label>
            <select name="parent_id" id="parent_menu" class="form-control">
              <option value="">Chọn Menu Cha (Nếu có)</option>
              @foreach($containerMenuOptions as $menuOption)
                <!-- Chỉ hiển thị các menu option là menu cha -->
                @if($menuOption->isParent())
                  <option value="{{ $menuOption->id }}" data-parent-menu="{{ $menuOption->definition_id }}">{{ $menuOption->name }}</option>
                @endif
              @endforeach
            </select>
          </div>

          <!-- Menu Con Dropdown -->
          <div class="form-group">
            <label for="child_menu">Menu Con:</label>
            <select name="child_id" id="child_menu" class="form-control">
              <option value="">Chọn Menu Con (Dựa trên Menu Cha)</option>
              <!-- Các option menu con sẽ được thêm vào đây dựa trên JavaScript -->
            </select>
          </div>

          <!-- Checkbox Thùng Trưng Bày -->
            <input type="checkbox" name="display_box" id="display_box"> Tạo Trưng Bày

          <!-- Checkbox Tạo 2 chi nhánh -->
          <input type="checkbox" name="branch_box" id="branch_box"> Tạo 2 chi nhánh

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            <button type="submit" class="btn btn-primary">Lưu</button> 

          </div>
        </form>
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

<div class="container mt-3">
    <form id="searchProduct" action="" method="POST" class="mb-3">
        <div class="form-row">
            <!-- Tìm kiếm theo sản phẩm -->
            <div class="form-group col-md-6 col-sm-12">
              <label for="search_product_id">Nhập sản phẩm:</label>
              <div class="input-group">
                  <select name="search_product_id" id="search_product_id" class="form-control selectpicker" data-live-search="true">
                      <option value="">Chọn Sản Phẩm</option>
                      @foreach($products as $product)
                          <option value="{{ $product->id }}">{{ $product->name }}</option>
                      @endforeach
                  </select>
                  <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="button" onclick="clearSelection()">Xóa</button>
                  </div>
              </div>
            </div>

            <!-- Tìm kiếm theo vị trí -->
            <div class="row">
              <div class="form-group col-6 col-sm-6">
                  <label for="location_id">Lọc vị trí:</label>
                  <select name="location_id" id="location_id" class="form-control selectpicker" data-live-search="true">
                      <option value="">Chọn vị trí</option>
                      @foreach($locations as $location)
                          @if($location->isParent())
                              <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                          @endif
                      @endforeach
                  </select>
              </div>
              <div class="form-group col-6 col-sm-6">
                  <label for="child_location_id">Lọc thùng số:</label>
                  <select name="child_location_id" id="child_location_id" class="form-control selectpicker" data-live-search="true">
                      <option value="">Chọn thùng số</option>
                      @foreach($locations as $location)
                          @if($location->isChild())
                              <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                          @endif
                      @endforeach
                  </select>
              </div>
          </div>
          </form>

          <!-- Nút tìm kiếm và lọc theo chi nhánh, trạng thái container và loại bundle -->
          <div class="form-row">
            <div class="form-group col-md-3 col-sm-6"> <!-- Sử dụng col-md-3 để đảm bảo 4 phần tử cùng một hàng trên desktop -->
                <button type="submit" class="btn btn-primary btn-block">Tìm sản phẩm</button>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <select class="form-control" name="branch_id">
                    <option value="">Chọn chi nhánh</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <select class="form-control" name="container_status_id">
                    <option value="">Chọn trạng thái container</option>
                    @foreach($containerStatuses as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <select class="form-control" name="bundle_type">
                    <option value="">Chọn loại bundle</option>
                    <!-- Thêm các option cho loại bundle ở đây -->
                </select>
            </div>
          </div>

        </div>

    <!-- Kết quả tìm kiếm ở đây -->
</div>


<div class="container mt-3">
    <form id="searchContainer" action="" method="POST" class="form-inline">
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Nhập mã thùng" name="container_id" id="search_container_id">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">Tìm thùng</button>
                <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()">Xóa</button>
            </div>
        </div>

        <div class="form-check mb-3 mx-2">
            <input class="form-check-input" type="checkbox" id="display" name="display" value="1">
            <label class="form-check-label" for="display">
                Thùng Trưng Bày
            </label>
        </div>
        <div class="form-check mb-3 mx-2">
            <input class="form-check-input" type="checkbox" id="stock" name="stock" value="1">
            <label class="form-check-label" for="stock">
                Thùng Kho
            </label>
        </div>
        <button id="refesh_data" class="btn btn-secondary mb-3">Xóa bộ lọc</button>
    </form>
</div>



<!-- Hiển thị dữ liệu trong table -->    
<!-- Phần chứa phân trang -->
<nav aria-label="Page navigation example" id="paginationContainer">
  <ul class="pagination" id="pagination">
    <!-- Các liên kết phân trang sẽ được thêm vào đây -->
  </ul>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <table id="myTable" class="table">
                <thead>
                    <tr>
                      <th></th> <!-- Cột cho dấu "+" -->    
                      <th scope="col">ID thùng</th>
                      <th scope="col">Số lượng</th>
                      <th scope="col">Vị trí</th>
                      <th scope="col">Thùng số</th>
                      <th scope="col">Tên sản phẩm</th>
                    </tr>
                </thead>
                <tbody>
                    <!--@forelse ($containers as $container)
                    <tr>
                       <td class="expand-button" data-target="#details{{ $container->id }}">></td>    
                      <td>{{ $container->id }}</td>
                      <td>{{ $container->product_quantity }}</td>
                      <td>{{ $container->location->parent->location_name ?? 'null' }}</td>
                      <td>{{ $container->location->location_name ?? 'null' }}</td>
                      <td>{{ $container->productapi->name }}</td>
                    </tr>

                    <tr id="details{{ $container->id }}" class="details-row">
                        <td></td>
                        <td colspan="5">
                            <div><strong>Cập nhật:</strong> {{ $container->updated_at }}</div>
                            <div><strong>SKU:</strong> {{ $container->productapi->sku }}</div>
                            <div><strong>Đơn vị:</strong> {{ $container->unit }}</div>
                            <div><strong>Chi nhánh:</strong> {{ $container->branch->name }}</div>
                            <div><strong>Ghi chú:</strong> {{ $container->notes }}</div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="3">Không tìm thấy thùng hàng nào.</td>
                        </tr>
                    @endforelse -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection



@push('scripts')
<script>
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
</script>
@endpush

@push('scripts')
<script>
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
</script>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {//ẩn hiện nút mở rộng xem chi tiết
        // Sử dụng delegation event để gán sự kiện click cho các nút mở rộng, hiện tại và tương lai
        $('#myTable').on('click', '.expand-button', function() {
            var targetId = $(this).data('target');
            $(targetId).toggle(); // Toggle hiển thị của chi tiết
        });
    });
  //-----------chức năng tìm kiếm ajax---------------
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  // Tìm sản phẩm
  $(document).ready(function() {
    $("#searchProduct").on('submit', function(e) {
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
  });

  // Tìm mã thùng
  $(document).ready(function() {
    $("#searchContainer").on('submit', function(e) {
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
  });


//-------------------------
function updateTableContent(data) {
    $("#myTable tbody").empty(); // Xóa nội dung hiện tại của bảng
    if (data && data.length > 0) {
        data.forEach(function(container) {
            // Tạo một hàng mới với dữ liệu container
            var newRow = `
                <tr>
                    <td class="expand-button" data-target="#details${container.id}">></td>    
                    <td>${container.container_id}</td>
                    <td>${container.product_quantity}</td>
                    <td>${container.location && container.location.parent ? container.location.parent.location_name : 'null'}</td>
                    <td>${container.location ? container.location.location_name : 'null'}</td>
                    <td>${container.productapi.name}</td>
                </tr>
                <tr id="details${container.id}" class="details-row" style="display: none;">
                    <td></td>
                    <td colspan="5">
                        <div><strong>Cập nhật:</strong> ${container.updated_at}</div>
                        <div><strong>SKU:</strong> ${container.productapi.sku}</div>
                        <div><strong>Đơn vị:</strong> ${container.unit}</div>
                        <div><strong>Chi nhánh:</strong> ${container.branch.name}</div>
                        <div><strong>Ghi chú:</strong> ${container.notes}</div>
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
    updateTableContent(containers.data);//cập nhật data ban đầu
    updatePagination(containers);// Cập nhật phân trang ban đầu
    $('#pagination').on('click', 'a', function(e) {
        e.preventDefault();
        // Sử dụng data attribute để lấy số trang thay vì href
        var pageNumber = $(this).data('page');
        // Xây dựng URL cho trang cụ thể dựa trên số trang
        //console.log(currentType); 
        if (currentType === 'searchProduct') {
            var pageUrl = path + '?page=' + pageNumber; // Thay đổi đường dẫn phù hợp với API của bạn
        } else if (currentType === 'container') {
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
    currentType = 'container';
    currentData = containers;
    //console.log(currentData);
    loadPageData(origin_path);
}

</script>
@endpush