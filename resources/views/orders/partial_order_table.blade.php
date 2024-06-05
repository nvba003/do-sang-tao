<div class="bg-white shadow-md rounded-lg my-1 overflow-x-auto p-2 sm:p-4">
    <div>Đã chọn: <span class="bg-blue-500 text-white px-2 py-0 rounded-lg" x-text="selectedCount"></span> hàng</div>
    <!-- <button @click="updateOrders" class="bg-green-500 text-white px-4 py-2 rounded mb-4">Update orders</button> -->
    <table class="w-full bg-white border border-gray-200 rounded-lg">
        <thead class="text-white bg-gray-500">
            <tr>
                <th scope="col" class="w-1/24 px-2 py-2 mt-1 text-center text-xs md:text-sm hidden sm:block font-semibold uppercase tracking-wider">
                    <input type="checkbox" x-model="checkAll" @click="toggleAll">
                </th>
                <th scope="col" class="w-1/24 px-2 py-2 text-left text-center text-xs md:text-sm font-semibold uppercase tracking-wider"></th>
                <th scope="col" class="w-4/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Mã đơn</th>
                <th scope="col" class="w-4/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Khách hàng</th>
                <th scope="col" class="w-2/24 px-2 py-2 text-center text-xs md:text-sm font-semibold uppercase tracking-wider">Tổng tiền</th>
                <th scope="col" class="w-4/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Trạng thái</th>
                <th scope="col" class="w-1/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Đ.Gói</th>
                <th scope="col" class="w-1/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">T.Toán</th>
                <th scope="col" class="w-3/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Phụ trách</th>
                <th scope="col" class="w-3/24 px-2 py-2 text-left text-xs md:text-sm font-semibold uppercase tracking-wider">Kênh</th>
            </tr>
        </thead>
        
    <template x-for="order in orders" :key="order.id">
        <tbody>
            <tr :class="{'bg-blue-200': order.showDetails, 'bg-white': !order.showDetails}" class="border-b">
                <td class="p-2 w-1/24 text-center mt-2 hidden sm:block">
                    <input type="checkbox" class="checkItem" :value="order.id" x-model="selectedItems" @change="updateCount">
                </td>
                <td class="p-2 w-1/24 whitespace-nowrap">
                    <button @click="order.showDetails = !order.showDetails" x-text="order.showDetails ? '-' : '+'" class="bg-blue-500 w-6 h-6 text-white rounded"></button>
                </td>
                <td class="w-4/24 px-2 py-3 whitespace-nowrap" x-text="order.order_code"></td>
                <td class="w-4/24 px-2 py-3 whitespace-nowrap" x-text="order.customer_account.account_name"></td>
                <td class="w-2/24 px-2 py-3 whitespace-nowrap text-right mr-2" x-text="formatAmount(order.total_amount)"></td>
                <td class="w-4/24 px-2 py-3 whitespace-nowrap" x-text="getStatus(order.order_process ? order.order_process.status_id : '_')"></td>
                <td class="w-1/24 px-2 py-3 whitespace-nowrap" x-text="order.auxpacking_order ? '✔️' : ''"></td>
                <td class="w-1/24 px-2 py-3 whitespace-nowrap text-red-500" x-text="getPaymentStatus(order.payment)"></td>
                <td class="w-3/24 px-2 py-3 whitespace-nowrap" x-text="order.order_process ? order.order_process.user.name : '_'"></td>
                <td class="w-3/24 px-2 py-3 whitespace-nowrap" x-text="order.platform.name"></td>
            </tr>
            <template x-if="order.showDetails">
                <tr class="bg-blue-100 border-b">
                    <td colspan="100%" class="text-xs md:text-base">
                        <div class="flex flex-col w-full">
                            <div class="flex w-full mb-2">
                                <div class="flex-1 p-4 m-2 bg-gray-50 rounded-lg shadow">
                                    <div class="flex flex-wrap -mx-2">
                                        <!-- NVC: -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'carrier_' + order.id" class="text-gray-700 text-xs">NVC:</label>
                                            <select :id="'carrier_' + order.id" class="bg-white text-xs rounded py-2 px-6 w-full" x-model="order.order_process.carrier_id">
                                                <option value="">Chọn</option>
                                                @foreach($carriers as $carrier)
                                                    <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Phụ trách: -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'responsible_' + order.id" class="text-gray-700 text-xs">Phụ trách:</label>
                                            <select :id="'responsible_' + order.id" class="bg-white text-xs rounded py-2 px-8 w-full" x-model="order.order_process.responsible_user_id">
                                                <option value="">Chọn</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Mã KH: -->
                                        <div x-data="autocompleteSetup(order.customer_account.customer_id)" x-init="initAutocomplete" class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'customer_' + order.id" class="text-gray-700 text-xs">Mã KH:</label>
                                            <input :id="'customer_' + order.id" x-ref="customerInput" type="text" class="bg-white text-xs rounded p-2 w-full"  x-model="selectedCustomerLabel">
                                        </div>
                                        <!-- Vận đơn: -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'tracking_' + order.id" class="text-gray-700 text-xs">Vận đơn:</label>
                                            <input :id="'tracking_' + order.id" type="text" class="bg-white text-xs rounded p-2 w-full" x-model="order.order_process.tracking_number">
                                        </div>
                                        <!-- Loại đơn: -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'orderType_' + order.id" class="text-gray-700 text-xs">Loại đơn:</label>
                                            <select :id="'orderType_' + order.id" class="bg-white text-xs rounded py-2 px-6 w-full" x-model="order.order_type_id">
                                                <option value="">Chọn</option>
                                                @foreach($orderTypes as $orderType)
                                                    <option value="{{ $orderType->id }}">{{ $orderType->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Nguồn đơn từ: -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'source_' + order.id" class="text-gray-700 text-xs">Nguồn đơn từ:</label>
                                            <input :id="'source_' + order.id" type="text" class="bg-white text-xs rounded p-2 w-full" x-model="order.source_info">
                                        </div>
                                        <!-- Trạng thái: -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'orderStatus_' + order.id" class="text-gray-700 text-xs">Trạng thái:</label>
                                            <select :id="'orderStatus_' + order.id" class="bg-white text-xs rounded py-2 px-6 w-full" x-model="order.order_process.status_id">
                                                <option value="">Chọn</option>
                                                @foreach($orderStatuses as $orderStatus)
                                                    <option value="{{ $orderStatus->id }}">{{ $orderStatus->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Ghi chú: -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2">
                                            <label :for="'notes_' + order.id" class="text-gray-700 text-xs">Ghi chú:</label>
                                            <textarea :id="'notes_' + order.id" class="bg-white text-xs rounded p-2 w-full" x-model="order.notes" rows="2"></textarea>
                                        </div>
                                        <!-- Button Update -->
                                        <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-2 mt-4">
                                            <button class="bg-green-600 hover:bg-green-800 text-white text-xs font-bold py-2 px-4 rounded w-full" @click="updateInfo(order)">Cập nhật</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1 p-4 m-2 bg-gray-50 rounded-lg shadow">
                                    <div class="space-y-2">
                                        <div class="flex items-center space-x-2 mb-2" x-data="{ financeAmount: '' }">
                                            <label for="'notes_' + order.id" class="text-gray-700 text-sm font-medium">Thanh toán:</label>
                                            <input type="number" id="'notes_' + order.id" x-model="financeAmount" class="bg-white text-sm rounded p-2 border-gray-300" placeholder="Nhập số tiền">
                                            <button @click="addFinance(financeAmount)" class="text-sm bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">Thêm</button>
                                            <button @click="addFinance(order.total_amount)" class="text-sm bg-green-500 hover:bg-green-700 text-white ml-2 py-2 px-4 rounded">Thanh toán đủ</button>
                                        </div>
                                        <table class="min-w-full leading-normal">
                                            <thead>
                                                <tr class="bg-gray-100">
                                                    <th class="px-5 py-2 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                        Cần trả
                                                    </th>
                                                    <th class="px-5 py-2 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                        Đã trả
                                                    </th>
                                                    <th class="px-5 py-2 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                        Còn lại
                                                    </th>
                                                    <th class="px-5 py-2 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                        Ngày tạo
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Template cho thanh toán -->
                                                <template x-for="finance in order.finances" :key="finance.id">
                                                    <tr>
                                                        <td class="px-5 py-2 border-b border-gray-200 bg-white text-sm">
                                                            <p class="text-gray-900 whitespace-no-wrap" x-text="finance.amount_due"></p>
                                                        </td>
                                                        <td class="px-5 py-2 border-b border-gray-200 bg-white text-sm">
                                                            <p class="text-gray-900 whitespace-no-wrap" x-text="finance.amount_paid"></p>
                                                        </td>
                                                        <td class="px-5 py-2 border-b border-gray-200 bg-white text-sm">
                                                            <p class="text-gray-900 whitespace-no-wrap" x-text="finance.amount_remaining"></p>
                                                        </td>
                                                        <td class="px-5 py-2 border-b border-gray-200 bg-white text-sm">
                                                            <p class="text-gray-900 whitespace-no-wrap" x-text="formatDate(finance.created_at)"></p>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full p-2 rounded-lg">
                                <table class="w-full bg-gray-100 border border-gray-200 rounded-lg">
                                    <thead class="bg-blue-500 text-white text-sm rounded-lg">
                                        <tr>
                                            <th class="py-2 px-2 text-right font-normal">STT</th>    
                                            <th class="py-2 px-2 text-left font-normal">SKU</th>
                                            <th class="py-2 px-1 text-left font-normal">Tên Sản phẩm</th>
                                            <th class="py-2 px-1 text-center font-normal">SL</th>
                                            <th class="py-2 px-1 text-left font-normal">Kho/SP</th>
                                            <th class="py-2 px-1 text-center font-normal">Giá</th>
                                            <th class="py-2 px-1 text-center font-normal">%CK</th>
                                            <th class="py-2 px-1 text-center font-normal">CK</th>
                                            <th class="py-2 px-1 text-center font-normal">Tổng</th>
                                            <th class="py-2 px-1 text-center font-normal">CTKM</th>
                                            <th class="py-2 px-1 text-center font-normal">Bộ</th>
                                            <th class="py-2 px-1 text-center font-normal">Hủy</th>
                                            <th class="py-2 px-1 text-center font-normal">Xóa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(detail, index) in order.details" :key="detail.id">
                                            <tr class="border-t border-gray-200" :class="{'bg-red-200 text-gray-500': detail.is_cancelled }">
                                                <td class="py-2 px-2 w-1/24 text-sm text-right mr-2" x-text="index + 1"></td>
                                                <!-- <td class="py-2 px-2 w-1/24 text-sm" x-text="detail.product.product_api_id"></td> -->
                                                <td class="py-2 px-2 w-3/24" x-data="autocompleteProductSetup(detail)" x-init="initAutocompleteProduct">
                                                    <input :id="'product_' + detail.id" x-ref="productInput" type="text" class="bg-white text-xs rounded p-2 w-full"
                                                        x-model="detail.product.sku">
                                                </td>
                                                <td class="py-2 px-2 w-9/24" >
                                                    <div x-text="detail.product.name" class="text-xs"></div>
                                                    <div>
                                                        <input x-model="detail.notes" class="text-xs p-1 border-none rounded w-full bg-gray-50 italic" type="text">
                                                    </div>
                                                </td>
                                                <td class="py-2 px-1 w-1/24">
                                                    <input x-model="detail.quantity" class="text-xs p-1 border rounded w-14" type="number" min="0" @change="updateOrderTotal(order)">
                                                </td>
                                                <td class="py-2 px-2 w-1/24 items-center">
                                                    <div class="flex rounded px-1" :class="checkQtyContainer(detail)">
                                                        <p class="text-gray-900 text-xs whitespace-no-wrap" x-text="calculateTotalQtyContainers(detail)"></p>
                                                        <span class="text-gray-900 text-xs">/</span>
                                                        <p class="text-gray-900 text-xs whitespace-no-wrap" x-text="detail.product.quantity"></p>
                                                    </div>
                                                    <button @click="openProductDetails(detail.product.product_api_id, detail.order_id)" x-text="calculateTotalQtyDiff(detail.product.product_api_id, detail.order_id)" class="bg-blue-500 text-white text-xs py-0 px-2 rounded"></button>
                                                    <!-- Modal Component -->
                                                    <div x-show="openModalDiff" @click.away="openModalDiff = false" x-show.transition.opacity="openModalDiff" class="fixed inset-0 bg-gray-500 bg-opacity-25 flex justify-center items-center z-50">
                                                        <div class="bg-white p-6 rounded-lg shadow-lg text-black w-1/2 max-w-3xl">
                                                            <h2 class="text-lg font-bold mb-4">Sản phẩm trong các đơn hàng khác</h2>
                                                            <div class="overflow-x-auto">
                                                                <table class="min-w-full leading-normal">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mã đơn</th>
                                                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Số lượng</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <template x-for="order in selectedProductDetails" :key="order.order_id">
                                                                            <tr>
                                                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm" x-text="order.order_code"></td>
                                                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm" x-text="order.quantity"></td>
                                                                            </tr>
                                                                        </template>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="flex justify-end">
                                                                <button @click="openModalDiff = false" class="mt-4 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 text-right justify-end">Đóng</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-2 px-1 w-1/24">
                                                    <input x-model="detail.price" class="text-xs p-1 border rounded w-16" type="number" min="0" @change="updateOrderTotal(order)">
                                                </td>
                                                <td class="py-2 px-1 w-1/24">
                                                    <input x-model="detail.discount_percent" class="text-xs p-1 border rounded w-12" type="number" min="0.00" @input="detail.discount = 0; updateOrderTotal(order);">
                                                </td>
                                                <td class="py-2 px-1 w-1/24">
                                                    <input x-model="detail.discount" class="text-xs p-1 border rounded w-16" type="number" min="0" @input="detail.discount_percent = 0; updateOrderTotal(order);">
                                                </td>
                                                <td class="py-2 px-1 w-1/24 text-right text-sm font-medium" x-text="formatAmount(detail.total)"></td>
                                                <td class="py-2 px-1 w-2/24 text-center">
                                                    <select class="bg-white text-xs rounded py-2 px-6 w-full" x-model="detail.promotion_id">
                                                        <option value="">Chọn</option>
                                                        @foreach($promotions as $promotion)
                                                            <option value="{{ $promotion->id }}">{{ $promotion->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="py-2 px-1 w-1/24 text-center">
                                                    <button @click="toggleModal(detail)" x-show="detail.bundle_id" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-0 px-2 rounded" x-text="detail.bundles ? calculateMinimumProductQuantity(detail.bundles) : ''"></button>
                                                    <!-- Modal Component -->
                                                    <div x-show="detail.openModal" @click.away="detail.openModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center z-50">
                                                        <div class="bg-white p-6 rounded-lg shadow-lg text-black w-1/2 max-w-3xl transform transition-all ease-out duration-300" x-show.transition.opacity="detail.openModal">
                                                            <div class="overflow-x-auto">
                                                                <table class="min-w-full leading-normal">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                                                SKU
                                                                            </th>
                                                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                                                Tên sản phẩm
                                                                            </th>
                                                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                                                SL
                                                                            </th>
                                                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                                                Giá lẻ
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <template x-for="bundle in detail.bundles" :key="bundle.product_api_id">
                                                                            <tr class="hover:bg-gray-100">
                                                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                                                    <p class="text-gray-900 whitespace-no-wrap" x-text="bundle.product.sku"></p>
                                                                                </td>
                                                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                                                    <p class="text-gray-900 whitespace-no-wrap" x-text="bundle.product.name"></p>
                                                                                </td>
                                                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                                                    <p class="text-gray-900 whitespace-no-wrap" x-text="bundle.quantity"></p>
                                                                                </td>
                                                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                                                    <p class="text-gray-900 whitespace-no-wrap" x-text="bundle.product.price.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })"></p>
                                                                                </td>
                                                                            </tr>
                                                                        </template>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="flex justify-end">
                                                                <button @click="toggleModal(detail)" class="mt-4 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-2 px-1 w-1/24 text-center">
                                                    <input x-model="detail.is_cancelled" type="checkbox" @change="updateOrderTotal(order)" class="text-xs p-1 border rounded w-4">
                                                </td>
                                                <td class="py-2 px-1 w-1/24 text-center">
                                                    <button @click="deleteDetail(detail)" class="text-xs bg-red-500 text-white p-1 rounded px-2">x</button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr>
                                            <td colspan="9" class="py-2 px-2 text-left">
                                                <button @click="addDetail()" class="bg-blue-500 text-white px-4 py-2 mx-2 rounded">Thêm mới</button>
                                                <button x-bind:disabled="packingOrders.includes(order.id)"
                                                    x-text="packingOrders.includes(order.id) ? 'Đang đóng gói' : 'Chuyển đóng gói'"
                                                    @click="confirm('Bạn đã cập nhật đơn chưa?') && addPackingDetail()"
                                                    class="px-4 py-2 mx-2 rounded"
                                                    :class="{'bg-gray-500 cursor-not-allowed': packingOrders.includes(order.id), 'bg-yellow-500': !packingOrders.includes(order.id)}">
                                                </button>
                                            </td>
                                            <td colspan="4" class="py-2 px-2">
                                                <div class="flex justify-end">
                                                    <button @click="updateDetails()" class="bg-green-500 text-white px-4 py-2 rounded">Cập nhật</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="flex flex-col space-y-1.5 mt-4">
                                    <div class="flex justify-end items-center space-x-4">
                                        <label class="text-gray-700">Tiền hàng:</label>
                                        <span :id="'subtotal_' + order.id" class="bg-gray-200 text-xs rounded p-1 w-24 pr-5 text-right border border-gray-400" x-text="formatAmount(order.subtotal)"></span>
                                    </div>
                                    <div class="flex justify-end items-center space-x-4">
                                        <label :for="'discount_percent_' + order.id" class="text-gray-700">% Giảm giá đơn hàng:</label>
                                        <input type="number" :id="'discount_percent_' + order.id" class="bg-gray-50 text-xs rounded p-1 w-24 text-right" min="0.00" x-model="order.discount_percent" @input="order.total_discount = order.subtotal*(order.discount_percent / 100); updateOrderTotal(order);">
                                    </div>
                                    <div class="flex justify-end items-center space-x-4">
                                        <label :for="'total_discount_' + order.id" class="text-gray-700">Giảm giá đơn hàng:</label>
                                        <input type="number" :id="'total_discount_' + order.id" class="bg-gray-50 text-xs rounded p-1 w-24 text-right" x-model="order.total_discount" @input="order.discount_percent = 0; updateOrderTotal(order);">
                                    </div>
                                    <div class="flex justify-end items-center space-x-4">
                                        <label :for="'shipping_fee_' + order.id" class="text-gray-700">Phí vận chuyển:</label>
                                        <input type="number" :id="'shipping_fee_' + order.id" class="bg-gray-50 text-xs rounded p-1 w-24 text-right" x-model="order.shipping_fee" @change="updateOrderTotal(order); order.customer_shipping_fee = order.shipping_fee;">
                                    </div>
                                    <div class="flex justify-end items-center space-x-4">
                                        <label :for="'customer_shipping_fee_' + order.id" class="text-gray-700">Phí vận chuyển khách trả:</label>
                                        <input type="number" :id="'customer_shipping_fee_' + order.id" class="bg-gray-50 text-xs rounded p-1 w-24 text-right text-orange-500" x-model="order.customer_shipping_fee" @change="updateOrderTotal(order)">
                                    </div>
                                    <div class="flex justify-end items-center space-x-4">
                                        <label class="text-gray-700">Tổng số tiền:</label>
                                        <span :id="'total_amount_' + order.id" class="bg-gray-200 text-xs rounded p-1 w-24 pr-5 text-right border border-gray-400" x-text="formatAmount(order.total_amount)"></span>
                                    </div>
                                    <div class="flex justify-end items-center space-x-4">
                                        <label :for="'tax_' + order.id" class="text-gray-700">% Thuế:</label>
                                        <input type="number" :id="'tax_' + order.id" class="bg-gray-50 text-xs rounded p-1 w-24 text-right" min="0.00" x-model="order.tax" @change="updateOrderTotal(order)">
                                    </div>
                                    <div class="flex justify-end items-center space-x-4" x-show="order.tax > 0">
                                        <label class="text-gray-700">Tiền sau thuế:</label>
                                        <span :id="'final_amount_' + order.id" class="bg-gray-200 text-xs rounded p-1 w-24 pr-5 inline-block text-right border border-gray-400" x-text="formatAmount(order.final_amount)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </template>
        </tbody>
    </template>


    </table>
</div>
