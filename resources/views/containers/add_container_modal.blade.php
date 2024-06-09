<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50" id="addContainerModal">
    <div class="relative top-10 mx-auto p-5 border w-full sm:w-1/2 shadow-lg rounded-md bg-white">
        <div class="text-center">
            <div class="flex items-center justify-between px-4 bg-green-200 rounded">
                <h3 class="text-lg leading-normal font-medium text-gray-900" id="addContainerModalLabel">Thêm Thùng Hàng Mới</h3>
                <button type="button" class="bg-transparent hover:bg-gray-200 text-gray-500 font-semibold py-2 px-2 rounded inline-flex items-center" onclick="closeModal()">
                    <svg class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <!-- Form -->
            <form action="{{ route('containers.store') }}" method="POST" id="addContainerForm">
                @csrf
                <div class="px-2 py-2 bg-white sm:p-2">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2 sm:col-span-1">
                            <label for="containerId" class="block text-sm text-left font-medium text-gray-700">Mã thùng:</label>
                            <input type="text" name="id" id="containerId" class="mt-1 bg-gray-100 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Nhập mã thùng">
                            <div id="containerCodeError" class="text-xs text-red-500 mt-1 hidden">Mã thùng đã tồn tại.</div>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label for="branchId" class="block text-sm text-left font-medium text-gray-700">Chi Nhánh:</label>
                            <select id="branchId" name="branch_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Chọn chi nhánh</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option> 
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-6 mt-4">
                        <div class="col-span-2">
                            <label for="productId" class="block text-sm text-left font-medium text-gray-700">Sản Phẩm:</label>
                            <input type="text" id="productId" class="shadow appearance-none border rounded mt-1 block w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập tên sản phẩm">
                            <input type="hidden" id="productIDValue" name="productId">
                            <div id="productSku" class="text-sm text-left text-green-500"></div>
                        </div>
                        <div class="col-span-2">
                            <label for="unit" class="block text-sm text-left font-medium text-gray-700">Đơn Vị:</label>
                            <input type="text" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="unit" name="unit" placeholder="Nhập đơn vị">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mt-4">
                        <div class="col-span-1">
                            <label for="parent_menu" class="block text-sm text-left font-medium text-gray-700">Menu Cha:</label>
                            <select name="parent_id" id="parent_menu" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Chọn Menu Cha (Nếu có)</option>
                                @foreach($categories as $menuOption)
                                    @if($menuOption->isParent())
                                        <option value="{{ $menuOption->id }}" data-parent-menu="{{ $menuOption->definition_id }}">{{ $menuOption->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-1">
                            <label for="child_menu" class="block text-sm text-left font-medium text-gray-700">Menu Con:</label>
                            <select name="child_id" id="child_menu" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Chọn Menu Con (Dựa trên Menu Cha)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between gap-6 mt-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="display_box" id="display_box" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="display_box" class="ml-2 block text-sm text-gray-900">
                                Tạo Trưng Bày
                            </label>
                            <input type="checkbox" name="branch_box" id="branch_box" class="ml-2 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" checked>
                            <label for="branch_box" class="ml-2 block text-sm text-gray-900">
                                Tạo 2 chi nhánh
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end px-2 py-2 bg-gray-100 text-right sm:px-4 mt-2">
                        <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded" onclick="closeModal()">
                        Đóng
                        </button>
                        <button type="submit" class="ml-3 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
