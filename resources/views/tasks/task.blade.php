@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="container max-h-full mx-auto px-2 sm:px-3 lg:px-4">
  <div id="taskTable" class="w-full" x-data="taskTable()">
    <div class="flex flex-wrap mx-auto mt-2 px-4 py-2 bg-white rounded shadow-md mb-4">
        <form id="searchTask" @submit.prevent="submitForm" class="w-full mb-1">
            <div class="flex flex-wrap -mx-2">
                <!-- Tìm mã CV -->
                <div class="w-full sm:w-1/4 md:w-3/12 xl:w-4/24 px-2 mb-2 md:mb-0">
                    <label for="searchTaskCode" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Tìm CV:</label>
                    <div class="relative">
                        <input type="text" id="searchTaskCode" x-model="searchParams.searchTaskCode" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nhập mã CV">
                        <div class="absolute inset-y-0 right-0 flex items-center px-1">
                            <button type="button" @click="searchParams.searchTaskCode = ''" class="bg-gray-200 hover:bg-gray-300 text-gray-500 text-sm p-2 rounded-r-md">Xóa</button>
                        </div>
                    </div>
                </div>
                <!-- Phân loại -->
                <div class="w-full sm:w-3/12 xl:w-4/24 px-2 mb-1 md:mb-0">
                    <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Phân loại:</label>
                    <select id="status" x-model="searchParams.status" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                    <option value="">Chọn</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                </div>
                <!-- Ngày tạo từ -->
                <div class="w-full sm:w-3/12 xl:w-4/24 px-0 mx-2 mb-2 md:mb-0">
                    <label for="searchCreatedAtFrom" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Ngày tạo từ:</label>
                    <input type="date" id="searchCreatedAtFrom" x-model="searchParams.searchCreatedAtFrom" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <!-- Ngày tạo đến -->
                <div class="w-full sm:w-3/12 xl:w-4/24 px-0 mx-2 mb-2 md:mb-0">
                    <label for="searchCreatedAtTo" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Ngày tạo đến:</label>
                    <input type="date" id="searchCreatedAtTo" x-model="searchParams.searchCreatedAtTo" class="text-sm shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <!-- Hạn xử lý -->
                <div class="w-full sm:w-3/12 xl:w-3/24 px-2 mb-1 md:mb-0">
                    <label for="dueDate" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-1">Hạn xử lý:</label>
                    <select id="dueDate" x-model="searchParams.dueDate" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 text-sm py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option value="">Chọn</option>
                        <option value="1">Còn hạn</option>
                        <option value="2">Trong ngày</option>
                        <option value="3">Quá hạn</option>
                    </select>
                </div>
                <!-- Nút Tìm -->
                <div class="w-full sm:w-2/12 xl:w-2/24 py-2 px-2 sm:mt-2 flex items-end">
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 w-full">Tìm</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tasks Display by Status -->
    <div class="flex flex-wrap -mx-2">
        <template x-for="(tasks, status) in tasksGroupedByStatus" :key="status">
            <div class="w-full md:w-1/4 px-2 mb-4">
                <div class="bg-stone-200 p-2 rounded-lg shadow">
                    <h2 class="font-bold text-lg mb-1 text-center" x-text="statusNames[status]"></h2>
                    <div class="space-y-2 overflow-y-scroll max-h-[75%]">
                        <template x-for="task in tasks">
                            <div class="bg-white rounded shadow p-3">
                                <div class="flex items-center space-x-2">
                                    <h3 class="font-bold" x-text="task.task_code"></h3>
                                    <p class="text-gray-600 text-sm" x-text="'- ' + formatDateTime(task.due_date)"></p>
                                </div>
                                <h4 class="font-medium" x-text="task.title"></h4>
                                <p x-text="task.description" class="line-clamp-2"></p>
                                <button @click="taskDetailModal(task.id)" class="text-sm border border-blue-500 text-blue-500 hover:bg-blue-100 hover:border-blue-800 py-1 px-3 rounded transition duration-150 ease-in-out">Xem</button>
                                <template x-if="canDeleteTask(task)">
                                    <button @click="deleteTask(task.id)" class="text-sm mt-2 border border-red-500 text-red-500 hover:bg-red-100 hover:border-red-800 py-1 px-3 ml-2 rounded transition duration-150 ease-in-out">Xóa</button>
                                </template>
                            </div>
                        </template>
                    </div>
                    <button @click="openNewTaskModal(status)" class="mt-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded w-full">
                        Thêm
                    </button>
                </div>
            </div>
        </template>
    </div>

    @include('tasks.task_new_modal', ['users' => $users, 'categories' => $categories])
    @include('tasks.task_detail_modal', ['users' => $users, 'categories' => $categories])
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

</div>
@endsection

@push('scripts')
<script>
// console.log(@json($categories));
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
    const urls = {
        windowURL: `{{ url('/') }}`,
        baseUrl: `{{ url('/tasks') }}`,
        orderBaseUrl: '/orders', // Đường dẫn cơ bản đến trang chi tiết đơn hàng
        productBaseUrl: '/products',
        customerBaseUrl: '/customers',
    };
    const today = new Date();
    const sevenDaysAgo = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 7);

    document.addEventListener('alpine:init', () => {
        Alpine.data('taskTable', () => ({
            userId: @json(auth()->user()->id),
            userRole: @json(auth()->user()->roles()->first()->name),
            users: @json($users),
            tasks: [],
            tasksGroupedByStatus: {},
            statusNames: @json($statuses),
            tags: @json($tags),
            filteredTags: [],
            newTag: '',
            newOrderNumber: '',
            newProductNumber: '',
            newCustomerCode: '',
            newRelatedTaskCode: '',
            newRelationType: '1',
            newTaskCode: '',
            newUserId: '', // ID của người phụ trách mới
            newUserRole: 3, // Mặc định là người phụ trách phụ
            openModal: false,
            openModalDetail: false,
            taskDetail: {},
            newComment: '',
            form: {
                title: '',
                description: '',
                category_id: '',
                due_date: today.toISOString().slice(0, 10),
                status: '',
                user: this.userId,
            },
            // currentPage: 1,  // Ensure currentPage is part of your data model
            // lastPage: 1,
            // perPage: 15,
            // links: '',
            searchParams: {
                searchCreatedAtFrom: sevenDaysAgo.toISOString().slice(0, 10), // Đặt ngày bắt đầu là 7 ngày trước
                searchCreatedAtTo: today.toISOString().slice(0, 10), // Đặt ngày kết thúc là hôm nay
                status: '',
                searchTaskCode: '',
            },
            toggleScanningArea() {
                this.showScan = !this.showScan;
            },
            toggleAll() {
                if (!this.checkAll) {
                    this.selectedItems = this.tasks.map(item => item.id);
                } else {
                    this.selectedItems = [];
                }
                this.updateCount();
            },
            updateCount() {
                this.selectedCount = this.selectedItems.length;
            },
            openNewTaskModal(status) {
                this.form.status = status;
                this.openModal = true;
            },
            groupTasksByStatus() {
                this.tasksGroupedByStatus = {1: [], 2: [], 3: [], 4: []};
                this.tasks.forEach(task => {
                    if (!this.tasksGroupedByStatus[task.status]) {
                        this.tasksGroupedByStatus[task.status] = [task];
                    } else {
                        this.tasksGroupedByStatus[task.status].push(task);
                    }
                });
            },
            init() {
                const initialData = JSON.parse(@json($initialData));
                console.log(initialData);
                this.tasks = initialData.tasks;
                //this.links = initialData.links;
                this.fetchData(urls.baseUrl);
                // Watch for changes to currentPage and fetch new data accordingly
                // this.$watch('currentPage', (newPage) => {
                //     this.fetchData(`${urls.baseUrl}?page=${newPage}`);
                // });
                console.log(this.tasks);
                // console.log(this.links);
                console.log(this.tasksGroupedByStatus);
            },
            submitFormNewTask() {
                console.log('Submitting:', this.form);
                fetch('{{ route('tasks.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error('Something went wrong');
                })
                .then(data => {
                    console.log(data.task);
                    this.tasks.push(data.task);//thêm mới
                    this.groupTasksByStatus();//chạy lại group
                    this.openModal = false;
                    this.form = {};
                    this.taskDetailModal(data.task.id);//mở chi tiết CV
                    // toggleModal(true); // Hiển thị modal khi có thông báo thành công
                    // setTimeout(function() {
                    //     toggleModal(false); // Ẩn modal sau 500ms
                    // }, 500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi chưa tạo được.');
                });
            },
            fetchData(baseUrl, params = this.searchParams) {
                const url = new URL(baseUrl);
                console.log(this.perPage);
                url.searchParams.set('perPage', this.perPage);
                // Add search parameters from the current state
                Object.entries(params).forEach(([key, value]) => {
                    if (value) {
                        url.searchParams.set(key, value);
                    }
                });
                //console.log(params);
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Mark the request as AJAX
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    //console.log(response);
                    return response.json();
                })
                .then(data => {
                    console.log(data.tasks);
                    this.tasks = data.tasks;
                    this.links = data.links;
                    this.groupTasksByStatus();
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            },
            submitForm() {
                console.log(this.searchParams);
                this.fetchData(urls.baseUrl);
                this.groupTasksByStatus();
            },
            formatNumber(number) {
                return new Intl.NumberFormat('vi-VN').format(number);
            },
            formatDateTime(dateString) {
                const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
                return new Date(dateString).toLocaleString('vi-VN', options);
            },
            formatDateTimeLocal(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                return `${year}-${month}-${day}T${hours}:${minutes}`;
            },
            formatDate(dateString) {
                if (!dateString) return null;
                const date = new Date(dateString);
                return date.toISOString().split('T')[0]; // Chuyển đổi chuỗi ISO sang định dạng yyyy-MM-dd
            },
            fetchTask(taskId) {
                fetch(`${urls.baseUrl}/${taskId}`) // Đảm bảo bạn có route và controller phù hợp để xử lý API call này
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(data);
                        this.taskDetail = data;
                        this.taskDetail.due_date = this.formatDateTimeLocal(this.taskDetail.due_date);
                        this.taskDetail.customer_contact_date = this.formatDateTimeLocal(this.taskDetail.customer_contact_date);
                        this.taskDetail.customer_response_date = this.formatDateTimeLocal(this.taskDetail.customer_response_date);
                        this.taskDetail.combinedRelations = this.combinedRelations();
                        this.$nextTick(() => {
                            this.adjustTextareaHeight(this.$refs.textarea);
                        });
                    })
                    .catch(error => {
                        console.error('Failed to fetch task details:', error);
                    });
            },

            taskDetailModal(taskId) {
                this.fetchTask(taskId);
                console.log(this.taskDetail);
                this.openModalDetail = true;
            },

            savetaskDetail() {
                fetch(`${urls.baseUrl}/${this.taskDetail.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.taskDetail)
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        throw new Error('Something went wrong');
                    })
                    .then(data => {
                        const index = this.tasks.findIndex(task => task.id === this.taskDetail.id);
                        if (index !== -1) {
                            this.tasks[index] = this.taskDetail;
                        }
                        this.groupTasksByStatus();
                        console.log(this.tasks);
                        toggleModal(true); // Hiển thị modal khi có thông báo thành công
                        setTimeout(function() {
                            toggleModal(false); // Ẩn modal sau 500ms
                        }, 500);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Lỗi khi lưu được.');
                    });
            },
            deleteTask(taskId) {
                if (confirm('Bạn có chắc chắn muốn xóa công việc này không?')) {
                    // Xóa công việc, ví dụ gọi API hoặc xử lý trực tiếp
                    fetch(`${urls.baseUrl}/${taskId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    }).then(response => {
                        if (response.ok) {
                            // Lọc ra công việc bị xóa khỏi danh sách
                            this.tasks = this.tasks.filter(task => task.id !== taskId);
                            this.groupTasksByStatus();
                        } else {
                            alert('Lỗi xảy ra khi xóa công việc.');
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        alert('Lỗi không xóa được.');
                    });
                }
            },

            addComment() {
                if (!this.newComment.trim()) return; // Kiểm tra nếu input trống thì không làm gì
                console.log(this.taskDetail);
                const comment = {
                    task_id: this.taskDetail.id,
                    comment: this.newComment,
                };
                fetch('{{ route('tasks.addComment') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(comment)
                })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error('Something went wrong');
                })
                .then(data => {
                    // console.log(data);
                    const comment = data.comment;
                    this.taskDetail.comments.unshift(comment); // ví dụ thêm comment vào danh sách hiện tại
                    this.newComment = ''; // Xóa input sau khi thêm
                    // toggleModal(true); // Hiển thị modal khi có thông báo thành công
                    // setTimeout(function() {
                    //     toggleModal(false); // Ẩn modal sau 500ms
                    // }, 500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi khi lưu được.');
                });
            },
            deleteComment(commentId) {
                if (!confirm("Bạn có chắc chắn muốn xóa bình luận này không?")) return;
                fetch('{{ route('tasks.deleteComment') }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        commentId: commentId,
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    this.taskDetail.comments = this.taskDetail.comments.filter(comment => comment.id !== commentId);
                    toggleModal(true); // Hiển thị modal khi có thông báo thành công
                    setTimeout(function() {
                        toggleModal(false); // Ẩn modal sau 500ms
                    }, 500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi không xóa được.');
                });
            },
            saveComment(comment){
                // console.log(comment);
                fetch('{{ route('tasks.updateComment') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        commentId: comment.id,
                        comment: comment.comment
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi không lưu được.');
                });
            },

            uploadAttachment() {
                const fileInput = this.$refs.file;
                if (!fileInput || !fileInput.files.length) {
                    alert('Vui lòng chọn một tệp để tải lên.');
                    return;
                }
                const file = fileInput.files[0];
                const formData = new FormData();
                formData.append('file', file);
                console.log(`File name: ${file.name}`);
                console.log(`File type: ${file.type}`);
                console.log(`File size: ${file.size} bytes`);
                fetch(`${urls.baseUrl}/${this.taskDetail.id}/attachments`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    console.log(data);
                    this.taskDetail.attachments.push(data.attachment);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi chưa tải lên được.');
                });
            },
            deleteAttachment(attachmentId) {
                if (!confirm("Bạn có chắc chắn muốn xóa không?")) return;
                fetch(`${urls.windowURL}/attachments/${attachmentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    this.taskDetail.attachments = this.taskDetail.attachments.filter(a => a.id !== attachmentId);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi không xóa được.');
                });
            },

            filterTags() {
                if (this.newTag.trim() === '') {
                    this.filteredTags = [];
                } else {
                    this.filteredTags = this.tags.filter(tag => tag.name.toLowerCase().includes(this.newTag.toLowerCase()));
                }
            },
            addOrSelectTag() {
                const existingTag = this.filteredTags.find(tag => tag.name.toLowerCase() === this.newTag.toLowerCase());
                if (existingTag) {
                    this.selectTag(existingTag);
                } else {
                    this.addTag();
                }
            },
            addTag() {
                if (this.newTag.trim() === '') {
                    alert('Chưa nhập tag');
                    return;
                }
                fetch(`${urls.baseUrl}/${this.taskDetail.id}/tags`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ name: this.newTag })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.tag) {
                        this.taskDetail.tags.push(data.tag);
                        this.newTag = '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi chưa lưu được.');
                });
            },
            selectTag(tag) {
                console.log(tag);
                fetch(`${urls.baseUrl}/${this.taskDetail.id}/tags`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ name: tag.name })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.tag) {
                        this.taskDetail.tags.push(data.tag);
                        this.newTag = '';
                        this.filteredTags = this.tags.filter(t => t.id !== tag.id); // Loại bỏ tag đã chọn khỏi danh sách lọc
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi chưa lưu được.');
                });
            },
            removeTag(tagId) {
                if (!confirm('Bạn có chắc chắn muốn xóa thẻ này không?')) {
                    return;
                }
                fetch(`${urls.windowURL}/tags/${this.taskDetail.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ tag_id: tagId })
                })
                .then(response => response.json())
                .then(data => {
                    this.taskDetail.tags = this.taskDetail.tags.filter(tag => tag.id !== tagId);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi không xóa được.');
                });
            },

            canEditTask() {
                return this.taskDetail && Array.isArray(this.taskDetail.users) && this.taskDetail.users.some(user => user.id === this.userId);
            },
            canDeleteTask(task) {
                // Kiểm tra nếu user hiện tại là admin hoặc là người tạo task
                return this.isAdmin || task.users.some(user => user.id === this.userId && user.pivot.role === 1);
            },
            canManageUser(user) {
                // Chỉ admin, người tạo và người phụ trách chính có thể quản lý user
                return this.isAdmin || this.isCreator || this.isPrimary;
            },
            canAddUser() {
                // Chỉ admin, người tạo và người phụ trách chính có thể thêm user
                return this.isAdmin || this.isCreator || this.isPrimary;
            },
            get isAdmin() {
                return this.userRole === 'admin';
            },
            get isCreator() {
                return this.taskDetail.users.some(user => user.id === this.userId && user.pivot.role === 1);
            },
            get isPrimary() {
                return this.taskDetail.users.some(user => user.id === this.userId && user.pivot.role === 2);
            },
            getRoleName(role) {
                switch (role) {
                    case 1: return 'Người tạo';
                    case 2: return 'PT chính';
                    case 3: return 'PT phụ';
                    default: return '_';
                }
            },
            getRoleClass(role) {
                switch (role) {
                    case 1: return 'bg-blue-500 text-white rounded-full px-2';
                    case 2: return 'bg-green-500 text-white rounded-full px-2';
                    case 3: return 'bg-gray-500 text-white rounded-full px-2';
                    default: return '';
                }
            },
            adjustTextareaHeight(textarea) {
                // Reset height to auto to correctly calculate the new height
                textarea.style.height = 'auto';
                // Set the height based on the scrollHeight
                textarea.style.height = `${textarea.scrollHeight}px`;
            },
            addUser() {
                if (!this.newUserId) {
                    alert('Vui lòng nhập thông tin.');
                    return;
                }
                // Gửi yêu cầu thêm người phụ trách tới server
                fetch(`${urls.baseUrl}/${this.taskDetail.id}/add-user`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ userId: this.newUserId, role: this.newUserRole })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        // Thêm người phụ trách mới vào danh sách
                        this.taskDetail.users.push(data.user);
                        this.newUserId = ''; // Clear input field
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi chưa thêm được.');
                });
            },
            // editUser(user) {
            //     this.editingUserId = user.id;
            //     this.newUserId = user.id;
            //     this.newUserRole = user.pivot.role;
            // },
            removeUser(userId) {
                if (!confirm('Bạn có chắc chắn muốn xóa người phụ trách này không?')) {
                    return;
                }
                // Gọi API để xóa người phụ trách
                fetch(`${urls.baseUrl}/${this.taskDetail.id}/remove-user/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Xóa người phụ trách khỏi danh sách
                        this.taskDetail.users = this.taskDetail.users.filter(user => user.id !== userId);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi chưa xóa được.');
                });
            },

            addOrder() {
                if (this.newOrderNumber.trim() === '') {
                    alert('Chưa nhập đơn hàng');
                    return;
                }
                fetch(`${urls.baseUrl}/add-order`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        taskId: this.taskDetail.id,
                        orderNumber: this.newOrderNumber
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Something went wrong');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    this.taskDetail.orders.push(data.order);
                    this.newOrderNumber = '';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi! Xem lại đúng mã đơn chưa?');
                });
            },
            removeOrder(orderId) {
                if (!confirm('Bạn chắc xóa?')) {
                    return;
                }
                fetch(`${urls.baseUrl}/remove-order/${orderId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        taskId: this.taskDetail.id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    this.taskDetail.orders = this.taskDetail.orders.filter(order => order.id !== orderId);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi chưa xóa được.');
                });
            },

            addProduct() {
                if (this.newProductNumber.trim() === '') {
                    alert('Chưa nhập mã sản phẩm');
                    return;
                }
                fetch(`${urls.baseUrl}/add-product`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        taskId: this.taskDetail.id,
                        productNumber: this.newProductNumber
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Something went wrong');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    this.taskDetail.products.push(data.product);
                    this.newProductNumber = '';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi! Xem lại đúng mã sản phẩm chưa?');
                });
            },
            removeProduct(productId) {
                if (!confirm('Bạn chắc xóa?')) {
                    return;
                }
                fetch(`${urls.baseUrl}/remove-product/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        taskId: this.taskDetail.id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    this.taskDetail.products = this.taskDetail.products.filter(product => product.id !== productId);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi chưa xóa được.');
                });
            },

            addCustomer() {
                if (this.newCustomerCode.trim() === '') {
                    alert('Chưa nhập mã khách hàng');
                    return;
                }
                fetch(`${urls.baseUrl}/add-customer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        taskId: this.taskDetail.id,
                        customerNumber: this.newCustomerCode
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Something went wrong');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    this.taskDetail.customers.push(data.customer);
                    this.newCustomerCode = '';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi! Xem lại đúng mã khách hàng chưa?');
                });
            },
            removeCustomer(customerId) {
                if (!confirm('Bạn chắc xóa?')) {
                    return;
                }
                fetch(`${urls.baseUrl}/remove-customer/${customerId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        taskId: this.taskDetail.id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    this.taskDetail.customers = this.taskDetail.customers.filter(customer => customer.id !== customerId);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi chưa xóa được.');
                });
            },

            combinedRelations() {
                const dependents = this.taskDetail.dependents || [];
                const dependencies = this.taskDetail.dependencies || [];
                const relatedTasks = this.taskDetail.related_tasks || [];
                // Tạo một Map để lọc bỏ các task trùng lặp
                const map = new Map();
                // Thêm các dependents vào map
                dependents.forEach(task => map.set(task.id, { ...task, relation_type: 1 }));
                // Thêm các dependencies vào map, nếu đã tồn tại thì bỏ qua
                dependencies.forEach(task => {
                    if (!map.has(task.id)) {
                        map.set(task.id, { ...task, relation_type: 1 });
                    }
                });
                // Thêm các relatedTasks vào map
                relatedTasks.forEach(task => {
                    if (!map.has(task.id)) {
                        map.set(task.id, { ...task, relation_type: 2 });
                    }
                });
                // Trả về mảng các task từ map
                return Array.from(map.values());
            },
            getRelationType(type) {
                switch (type) {
                    case 1:
                        return 'Phụ thuộc';
                    case 2:
                        return 'Liên quan';
                    default:
                        return '_';
                }
            },
            addRelatedTask() {
                if (this.newRelatedTaskCode.trim() === '') {
                    alert('Chưa nhập mã công việc');
                    return;
                }
                fetch(`${urls.baseUrl}/add-related-task`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        taskId: this.taskDetail.id,
                        taskCode: this.newRelatedTaskCode,
                        relationType: this.newRelationType
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Something went wrong');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (this.newRelationType === '1') {
                    this.taskDetail.dependents.push(data.relatedTask);
                    } else if (this.newRelationType === '2') {
                        this.taskDetail.related_tasks.push(data.relatedTask);
                    } else {
                        this.taskDetail.dependencies.push(data.relatedTask);
                    }
                    this.taskDetail.combinedRelations.push(data.relatedTask);
                    this.newRelatedTaskCode = '';
                    this.newRelationType = '1'; // Reset to default
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi! Xem lại đúng mã công việc chưa?');
                });
            },
            removeRelatedTask(taskId) {
                if (!confirm('Bạn chắc chắn muốn xóa công việc liên quan này?')) {
                    return;
                }
                fetch(`${urls.baseUrl}/remove-related-task/${this.taskDetail.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        relatedTaskId: taskId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    this.taskDetail.related_tasks = this.taskDetail.related_tasks.filter(task => task.id !== taskId);
                    this.taskDetail.dependents = this.taskDetail.dependents.filter(task => task.id !== taskId);
                    this.taskDetail.dependencies = this.taskDetail.dependencies.filter(task => task.id !== taskId);
                    this.combinedRelations();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi chưa xóa được.');
                });
            },

        }));
    });
</script>
@endpush
