<!-- Modal for taskDetail Details -->
<div x-show="openModalDetail" class="fixed inset-0 overflow-y-auto flex items-center justify-center z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center overflow-hidden max-h-full w-full pt-4 px-4 pb-10 mt-12 w-full text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="openModalDetail = false"></div>
        <!-- Modal content -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-4xl sm:w-full"> <!-- Increase max-width -->
            <form @submit.prevent="savetaskDetail()" class="bg-white px-4 pt-4">
                <div class="sm:flex sm:items-start">
                    <!-- Column 1: Original content -->
                    <div class="sm:flex-1"> <!-- Adjusted for grid layout -->
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <div class="flex items-center space-x-2">
                                <span class="font-normal">Mã CV:</span>
                                <p x-text="taskDetail.task_code" class="px-4 block border-none rounded-md shadow-sm text-white bg-blue-500 font-medium"></p>
                                <p class="text-gray-600 text-sm" x-text="'Ngày tạo: ' + formatDateTime(taskDetail.created_at)"></p>
                            </div>
                            <input x-model="taskDetail.title" type="text" class="mb-1 mt-2 p-1 block w-full border-none hover:border-gray-300 rounded-md shadow-sm font-bold" :disabled="!canEditTask()">
                            <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                            <!-- <textarea x-model="taskDetail.description" class="mb-1 block w-full border-gray-300 rounded-md shadow-sm" :disabled="!canEditTask()"></textarea> -->
                            <textarea 
                                x-model="taskDetail.description" 
                                class="mb-1 block w-full border-gray-300 rounded-md shadow-sm overflow-hidden resize-none"
                                :disabled="!canEditTask()"
                                x-ref="textarea"
                                @input="adjustTextareaHeight($refs.textarea)"
                            ></textarea>
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                                    <select x-model="taskDetail.status" class="mb-1 h-7 p-1 text-sm block w-full border-gray-300 rounded-md shadow-sm"
                                        :class="{
                                            'bg-gray-200': taskDetail.status == 1, 
                                            'bg-yellow-200': taskDetail.status == 2,
                                            'bg-orange-300': taskDetail.status == 3,
                                            'bg-green-200': taskDetail.status == 4
                                        }"
                                        :disabled="!canEditTask()">
                                        <template x-for="(status, id) in statusNames">
                                            <option :value="parseInt(id)" x-text="status"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Phân loại</label>
                                    <select x-model="taskDetail.category_id" class="mb-1 h-7 p-1 text-sm block w-full border-gray-300 rounded-md shadow-sm" :disabled="!canEditTask()">
                                        <option value="">Chọn</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Hạn xử lý</label>
                                    <input x-model="taskDetail.due_date" type="datetime-local" class="mb-1 h-7 text-xs bg-blue-200 block w-full border-gray-300 rounded-md shadow-sm" :disabled="!canEditTask()">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Kết quả</label>
                                    <select x-model="taskDetail.outcome" class="mb-1 h-7 p-1 text-sm block w-full border-gray-300 rounded-md shadow-sm" :disabled="!canEditTask()">
                                        <option value="">Chọn</option>    
                                        <option value="1">Thành công</option>
                                        <option value="2">Thất bại</option>
                                        <option value="3">Đợi KH phản hồi</option>
                                        <option value="4">Dang dở</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Ngày liên hệ khách hàng</label>
                                    <input x-model="taskDetail.customer_contact_date" type="datetime-local" class="mb-1 block w-full h-7 border-gray-300 rounded-md shadow-sm text-xs" :disabled="!canEditTask()">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Ngày khách hàng phản hồi</label>
                                    <input x-model="taskDetail.customer_response_date" type="datetime-local" class="mb-1 block w-full h-7 border-gray-300 rounded-md shadow-sm text-xs" :disabled="!canEditTask()">
                                </div>
                            </div>
                        </div>
                        <!-- Comments Section -->
                        <div class="mt-2">
                            <h4 class="text-md font-semibold">Bình luận</h4>
                            <div class="flex items-center space-x-2">
                                <textarea x-model="newComment" @keydown.enter.prevent="addComment()" placeholder="Nhập bình luận mới" class="flex-grow border-gray-300 rounded-md shadow-sm p-2 text-gray-700" rows="1"></textarea>
                                <button @click.prevent="addComment()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Thêm</button>
                            </div>
                            <template x-for="comment in taskDetail.comments" :key="comment.id">
                                <div class="mt-2 p-2 bg-gray-100 rounded text-sm">
                                    <!-- User Info and Comment -->
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-block h-6 w-6 rounded-full overflow-hidden bg-blue-500 uppercase text-white flex items-center justify-center"
                                            :class="{'bg-blue-500': comment.user.id !== userId, 'bg-yellow-500': comment.user.id === userId}">
                                            <span x-text="comment.user.name[0]"></span>
                                        </span>
                                        <span x-text="comment.user.name" class="font-semibold"></span>
                                        <span x-text="' ' + formatDateTime(comment.created_at)" class="text-gray-500 text-sm"></span>
                                    </div>
                                    <!-- Comment Text -->
                                    <div>
                                        <template x-if="comment.editing">
                                            <textarea x-model="comment.comment" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" rows="1"></textarea>
                                        </template>
                                        <template x-if="!comment.editing">
                                            <p class="text-gray-700" x-text="comment.comment"></p>
                                        </template>
                                    </div>
                                    <!-- Edit and Delete Buttons for the Comment Creator -->
                                    <div x-show="comment.user.id === userId" class="flex space-x-3 justify-end">
                                        <span x-text="comment.updated_at ? ('Đã sửa: ' + formatDateTime(comment.updated_at)) :''" class="text-gray-500 italic text-sm"></span>
                                        <button x-show="!comment.editing" @click.prevent="comment.editing = true" class="text-blue-500 hover:text-blue-700">Chỉnh sửa</button>
                                        <button x-show="comment.editing" @click.prevent="saveComment(comment); comment.editing = false" class="text-green-500 hover:text-green-700">Lưu</button>
                                        <button @click.prevent="deleteComment(comment.id)" class="text-red-500 hover:text-red-700">Xóa</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <!-- Tags -->
                        <div class="mt-0">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-md font-semibold mt-4">Tags</h4>
                                <div class="relative text-sm">
                                    <input x-model="newTag" @input="filterTags" type="text" class="border rounded px-2 py-1 h-7 text-sm" placeholder="Thêm tag">
                                    <button @click.prevent="addOrSelectTag()" class="ml-1 bg-green-500 hover:bg-green-700 text-white rounded px-2 py-1 h-7 mt-4">Thêm</button>
                                    <div class="absolute z-10 bg-white shadow-lg rounded mt-1 w-full" x-show="filteredTags.length > 0">
                                        <template x-for="tag in filteredTags" :key="tag.id">
                                            <div @click.prevent="selectTag(tag)" class="cursor-pointer p-2 hover:bg-gray-100">
                                                <span x-text="tag.name"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center">
                                <template x-for="tag in taskDetail.tags" :key="tag.id">
                                    <div class="relative m-1 bg-gray-200 hover:bg-gray-300 rounded-full px-2 font-bold text-sm leading-loose cursor-pointer flex items-center space-x-2">
                                        <span x-text="tag.name"></span>
                                        <button @click.prevent="removeTag(tag.id)" class="bg-red-500 text-white rounded-full p-1 hover:bg-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <!-- Column 2: New Data Section -->
                    <div class="sm:flex-1 sm:ml-4"> <!-- Adjusted for grid layout, added margin -->
                        <!-- User -->
                        <div class="bg-blue-50 rounded-lg shadow px-2 pb-2">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-md font-semibold mt-2">Phụ trách</h4>
                                <div class="relative text-sm" x-show="canAddUser()">
                                    <!-- Thêm người phụ trách mới -->
                                    <div class="flex items-center space-x-2 mt-2" >
                                        <select x-model="newUserId" class="border rounded px-2 py-1 h-7 min-w-20 text-sm">
                                            <option value="">Chọn</option>
                                            <template x-for="user in users" :key="user.id">
                                                <option :value="user.id" x-text="user.name"></option>
                                            </template>
                                        </select>
                                        <select x-model="newUserRole" class="border rounded px-2 py-1 h-7 text-sm">
                                            <option value="1">Người tạo</option>
                                            <option value="2">Phụ trách chính</option>
                                            <option value="3">Phụ trách phụ</option>
                                        </select>
                                        <button @click.prevent="addUser()" class="bg-green-500 hover:bg-green-700 text-white rounded px-2 py-1 h-7 text-sm">Thêm</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Danh sách người phụ trách -->
                            <ul class="space-y-2 flex flex-wrap">
                                <template x-for="user in taskDetail.users" :key="user.id">
                                    <li class="flex items-center justify-between p-1 text-sm bg-gray-200 rounded-full mr-2">
                                        <div class="flex items-center space-x-2">
                                            <span x-text="user.name"></span>
                                            <span :class="getRoleClass(user.pivot.role)" x-text="getRoleName(user.pivot.role)"></span>
                                        </div>
                                        <div class="flex space-x-2 ml-2" x-show="canManageUser(user)">
                                            <!-- <button @click.prevent="editUser(user)" class="bg-yellow-500 text-white rounded-full p-1 hover:bg-yellow-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9m-9-8h9m-9-8h9m-9 8H3m9-8H3m9 8H3m9 8H3m9-8H3m9-8H3" />
                                                </svg>
                                            </button> -->
                                            <button @click.prevent="removeUser(user.id)" class="bg-red-500 text-white rounded-full p-1 hover:bg-red-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        <!-- Attachments Section -->
                        <div class="mt-2">
                            <h4 class="text-md font-semibold">Đính kèm</h4>
                            <div class="flex items-center space-x-2">
                                <input type="file" x-ref="file" class="file:bg-blue-50 file:border file:border-blue-300 file:px-4 file:py-2 text-sm text-gray-700 file:text-blue-700 hover:file:bg-blue-100">
                                <button @click.prevent="uploadAttachment" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Tải lên</button>
                            </div>
                            <div class="space-y-2 mt-2 flex flex-wrap">
                                <template x-for="attachment in taskDetail.attachments" :key="attachment.id">
                                    <div class="mt-2 flex items-center space-x-2">
                                        <!-- Kiểm tra nếu là hình ảnh để hiển thị -->
                                        <template x-if="attachment.url.endsWith('.jpg') || attachment.url.endsWith('.png') || attachment.url.endsWith('.jpeg')">
                                            <div class="relative">
                                                <img :src="urls.windowURL + attachment.url" @click.prevent="window.open(urls.windowURL + attachment.url, '_blank')" class="w-24 h-24 rounded cursor-pointer" alt="Attachment Thumbnail">
                                                <button @click.prevent="deleteAttachment(attachment.id)" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 hover:bg-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <!-- Đối với file PDF và các file khác -->
                                        <template x-if="!attachment.url.endsWith('.jpg') && !attachment.url.endsWith('.png') && !attachment.url.endsWith('.jpeg')">
                                            <div class="flex flex-col items-start">
                                                <!-- Hiển thị icon tùy theo loại file -->
                                                <svg x-show="attachment.url.endsWith('.pdf')" @click.prevent="window.open(urls.windowURL + attachment.url, '_blank')" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="h-20 w-20 cursor-pointer">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                                </svg>
                                                <!-- Hiển thị tên file với giới hạn hiển thị -->
                                                <div class="relative">
                                                    <p @click.prevent="window.open(urls.windowURL + attachment.url, '_blank')" class="truncate w-24 text-blue-500 hover:text-blue-700 underline cursor-pointer text-sm" x-text="attachment.filename"></p>
                                                    <button @click.prevent="deleteAttachment(attachment.id)" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 hover:bg-red-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div> 
                        <!-- Related Orders -->
                        <div class="border-t">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-md font-semibold mt-1">Đơn hàng liên quan</h4>
                                <div class="relative text-sm">
                                    <div class="relative mt-2 flex items-center">
                                        <input x-model="newOrderNumber" type="text" class="border rounded px-2 py-1 w-full text-sm" placeholder="Nhập mã đơn">
                                        <button @click.prevent="addOrder()" class="ml-2 bg-green-500 hover:bg-green-700 text-white rounded px-2 py-1">Thêm</button>
                                    </div>
                                </div>
                            </div>
                            <table class="min-w-full bg-white mt-1" x-show="taskDetail.orders.length > 0">
                                <thead>
                                    <tr>
                                        <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Mã đơn</th>
                                        <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Tổng</th>
                                        <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-right text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="order in taskDetail.orders" :key="order.id">
                                        <tr>
                                            <td class="py-1 px-4 border-b border-gray-200 text-sm">
                                                <a :href="`${urls.orderBaseUrl}/${order.id}`" class="text-blue-500 hover:underline" x-text="order.order_code"></a>
                                            </td>
                                            <td class="py-1 px-4 border-b border-gray-200 text-sm" x-text="formatNumber(order.total_amount)"></td>
                                            <td class="py-1 px-4 border-b border-gray-200 text-right">
                                                <button @click.prevent="removeOrder(order.id)" class="bg-red-500 text-white rounded-full p-1 hover:bg-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <!-- Related Products -->
                        <div class="border-t">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-md font-semibold mt-1">Sản phẩm liên quan</h4>
                                <div class="relative mt-2 text-sm flex items-center" x-data="autocompleteProductSetup()" x-init="initAutocompleteProduct">
                                    <input type="text" x-ref="productInput" id="newProductNumber" x-model="displayProductName" 
                                        @blur="setTimeout(() => productSuggestions = [], 100)" class="text-sm shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập sản phẩm">
                                    <div class="absolute z-20 w-full bg-white mt-1 rounded-md shadow-lg" x-show="productSuggestions.length > 0" style="top: 100%;">
                                        <ul class="max-h-60 overflow-y-auto">
                                            <template x-for="product in productSuggestions" :key="product.id">
                                                <li @click="selectProduct(product)" class="p-2 hover:bg-gray-100 cursor-pointer">
                                                    <span x-text="product.name"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                    <!-- <input x-model="newProductNumber" type="text" class="border rounded px-2 py-1 w-full text-sm" placeholder="Nhập mã sản phẩm"> -->
                                    <button @click.prevent="addProduct()" class="ml-2 bg-green-500 hover:bg-green-700 text-white rounded px-2 py-1">Thêm</button>
                                </div>
                            </div>
                            <table class="min-w-full bg-white mt-1" x-show="taskDetail.products.length > 0">
                                <thead>
                                    <tr>
                                        <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Mã SP</th>
                                        <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Tên SP</th>
                                        <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-right text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="product in taskDetail.products" :key="product.id">
                                        <tr>
                                            <td class="py-1 px-4 border-b border-gray-200 text-sm">
                                                <a :href="`${urls.productBaseUrl}/${product.id}`" class="text-blue-500 hover:underline" x-text="product.sku"></a>
                                            </td>
                                            <td class="py-1 px-4 border-b border-gray-200 text-sm" x-text="product.name"></td>
                                            <td class="py-1 px-4 border-b border-gray-200 text-right">
                                                <button @click.prevent="removeProduct(product.id)" class="bg-red-500 text-white rounded-full p-1 hover:bg-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <!-- Related Customers -->
                        <div class="border-t">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-md font-semibold mt-1">Khách hàng liên quan</h4>
                                <div class="relative text-sm">
                                    <div class="relative mt-2 flex items-center">
                                        <input x-model="newCustomerCode" type="text" class="border rounded px-2 py-1 w-full text-sm" placeholder="Nhập mã khách hàng">
                                        <button @click.prevent="addCustomer()" class="ml-2 bg-green-500 hover:bg-green-700 text-white rounded px-2 py-1">Thêm</button>
                                    </div>
                                </div>
                            </div>
                            <table class="min-w-full bg-white mt-1" x-show="taskDetail.customers.length > 0">
                                <thead>
                                    <tr>
                                        <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Mã KH</th>
                                        <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Tên KH</th>
                                        <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-right text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="customer in taskDetail.customers" :key="customer.id">
                                        <tr>
                                            <td class="py-1 px-4 border-b border-gray-200 text-sm">
                                                <a :href="`${urls.customerBaseUrl}/${customer.id}`" class="text-blue-500 hover:underline" x-text="customer.customer_code"></a>
                                            </td>
                                            <td class="py-1 px-4 border-b border-gray-200 text-sm" x-text="customer.name"></td>
                                            <td class="py-1 px-4 border-b border-gray-200 text-right">
                                                <button @click.prevent="removeCustomer(customer.id)" class="bg-red-500 text-white rounded-full p-1 hover:bg-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <!-- Related Tasks -->
                        <div class="border-t">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-md font-semibold">CV liên quan</h4>
                                <div class="relative text-sm">
                                    <div class="relative mt-2 flex items-center">
                                        <input x-model="newRelatedTaskCode" type="text" class="border rounded px-2 py-1 text-sm w-24" placeholder="Nhập mã CV">
                                        <select x-model="newRelationType" class="ml-2 border rounded px-2 py-1 text-sm">
                                            <option value="1">Phụ thuộc</option>
                                            <option value="2">Liên quan</option>
                                        </select>
                                        <button @click.prevent="addRelatedTask()" class="ml-2 bg-green-500 hover:bg-green-700 text-white rounded px-2 py-1">Thêm</button>
                                    </div>
                                </div>
                            </div>
                            <table class="min-w-full bg-white mt-1" x-show="taskDetail.dependents.length > 0 || taskDetail.dependencies.length > 0 || taskDetail.related_tasks.length > 0">
                                <thead>
                                    <tr>
                                        <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Mã công việc</th>
                                        <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Loại Quan Hệ</th>
                                        <th class="py-1 px-4 border-b-2 border-gray-200 bg-gray-100 text-right text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="relation in combinedRelations" :key="relation.id">
                                        <tr>
                                            <td class="py-1 px-4 border-b border-gray-200 text-sm">
                                                <a :href="`${urls.baseUrl}/${relation.id}`" class="text-blue-500 hover:underline" x-text="relation.task_code"></a>
                                            </td>
                                            <td class="py-1 px-4 border-b border-gray-200 text-sm" x-text="getRelationType(relation.relation_type)"></td>
                                            <td class="py-1 px-4 border-b border-gray-200 text-right">
                                                <button @click.prevent="removeRelatedTask(relation.id)" class="bg-red-500 text-white rounded-full p-1 hover:bg-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- End of Column 2 -->
                </div> <!-- End of flex layout for two columns -->

                <!-- Action buttons at the bottom of the modal -->
                <div class="px-4 pt-3 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-500 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Lưu
                    </button>
                    <button @click="openModalDetail = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
