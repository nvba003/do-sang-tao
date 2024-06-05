<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task\Task;
use App\Models\Task\Tag;
use App\Models\Task\TaskCategory;
use App\Models\Task\TaskComment;
use App\Models\Task\TaskAttachment;
use App\Models\Task\TaskRelation;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // $perPage = $request->input('per_page',15);
        $categories = TaskCategory::all();
        $users = User::all();
        $tags = Tag::all();
        $statuses = [
            1 => 'Chưa bắt đầu',
            2 => 'Đang làm',
            3 => 'Đợi xử lý',
            4 => 'Hoàn thành'
        ];
        $query = Task::with(['category', 'users']);
        // Kiểm tra quyền admin
        if (!Auth::user()->hasRole('admin')) {
            // Nếu không phải admin, chỉ hiển thị tasks liên quan đến người dùng hiện tại
            $query->whereHas('users', function ($q) {
                $q->where('users.id', Auth::id());
            });
        }
        $query->when($request->filled('searchTaskCode'), function ($q) use ($request) {
            $q->where('task_code', $request->input('searchTaskCode'));
        })
        ->when($request->filled('status'), function ($q) use ($request) {
            $q->where('category_id', $request->input('status'));
        })
        ->when($request->filled('searchCreatedAtFrom'), function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->input('searchCreatedAtFrom'));
        })
        ->when($request->filled('searchCreatedAtTo'), function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->input('searchCreatedAtTo'));
        })
        ->when($request->has('dueDate'), function ($query) use ($request) {
            $dueDateFilter = $request->input('dueDate');
            $today = Carbon::today();
            switch ($dueDateFilter) {
                case 1: // Còn hạn
                    $query->where('due_date', '>', $today);
                    break;
                case 2: // Trong ngày
                    $query->whereDate('due_date', '=', $today);
                    break;
                case 3: // Quá hạn
                    $query->where('due_date', '<', $today);
                    break;
            }
        });
        // Thêm phân trang cho các truy vấn
        $tasks = $query->get();//->paginate($perPage);
        if ($request->ajax()) {
            return response()->json([
                'tasks' => $tasks,//->items(),
                //'links' => $tasks->links('vendor.pagination.custom-tailwind')->toHtml(),
            ]);
        }
        $initialData = json_encode([
            'tasks' => $tasks,//->items(),
            //'links' => $tasks->links('vendor.pagination.custom-tailwind')->toHtml(),
        ]);
        return view('tasks.task', compact('tasks', 'tags', 'statuses', 'categories', 'users', 'initialData'), [
            'header' => 'Quản lý công việc'
        ]);
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'title' => 'required|string|max:255',
        //     'description' => 'required|string',
        //     'due_date' => 'nullable|date',
        //     'category_id' => 'required|exists:categories,id'
        // ]);
        $task = new Task([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'category_id' => $request->category_id,
            'status' => $request->status,
        ]);
        $task->save();
        // Lấy category để lấy chữ cái đầu tiên của tên
        $category = TaskCategory::find($request->category_id);
        $firstLetter = strtoupper(substr($category->name, 0, 1));
        // Tạo task_code dựa trên chữ cái đầu tiên của tên category và ID của task
        $task->task_code = $firstLetter . sprintf("%04d", $task->id); // Padding ID để đảm bảo ít nhất có 4 chữ số
        $task->save();
        // Thêm người dùng vào task_users với vai trò là creator
        $task->users()->attach($request->user()->id, ['role' => 1]); // 1 là vai trò 'creator'
        return response()->json(['message' => 'Tạo mới thành công', 'task' => $task]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|integer|min:0|max:255',
            'category_id' => 'required|integer|exists:task_categories,id',
            'task_code' => 'nullable|string|unique:tasks,task_code,' . $id,
            'outcome' => 'nullable|integer|min:0|max:255',
            'customer_contact_date' => 'nullable|date',
            'customer_response_date' => 'nullable|date'
        ]);
        $task = Task::findOrFail($id);
        // Kiểm tra nếu user có liên quan trong task_users thì mới cho phép sửa
        if (!$task->users->contains(auth()->id())) {
            return response()->json(['message' => 'You do not have permission to update this task.'], 403);
        }
        $task->title = $request->title;
        $task->description = $request->description;
        $task->due_date = $request->due_date;
        $task->status = $request->status;
        $task->category_id = $request->category_id;
        $task->task_code = $request->task_code;
        $task->outcome = $request->outcome;
        $task->customer_contact_date = $request->customer_contact_date;
        $task->customer_response_date = $request->customer_response_date;
        $task->save();
        return response()->json(['message' => 'Task details updated successfully!', 'task' => $task]);
    }

    // Hiển thị chi tiết task
    public function show($id)
    {
        $task = Task::with([
            'comments' => function ($query) {
                $query->orderBy('created_at', 'desc'); // Sắp xếp các comment theo ngày tạo giảm dần
            },'comments.user',
            'attachments',
            'tags', 'orders', 'products', 'customers', 'users', 'dependents', 'dependencies', 'relatedTasks'])->findOrFail($id);
         // Thêm URL cho mỗi attachment
        $task->attachments->each(function ($attachment) {
            $attachment->url = Storage::url($attachment->file_path);
            // $attachment->name = basename($attachment->file_path);  // Lấy tên file từ đường dẫn
        });
        // Lấy người dùng chính
        // $primaryUser = $task->users()->wherePivot('is_primary', 1)->first();
        // $task->primaryUser = $primaryUser;
        // Lấy người dùng chính và người tạo công việc
        $primaryUser = $task->users()->wherePivot('role', 2)->first();
        $creator = $task->users()->wherePivot('role', 1)->first();
        $task->primaryUser = $primaryUser;
        $task->creator = $creator;

        return response()->json($task);
    }

    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();
            // Trả về phản hồi JSON cho các yêu cầu AJAX hoặc redirect nếu là yêu cầu truyền thống.
            return response()->json([
                'message' => 'Task deleted successfully!'
            ], 200);
        } catch (\Exception $e) {
            // Trả về lỗi nếu không tìm thấy task hoặc có lỗi xảy ra
            return response()->json([
                'message' => 'Error deleting task: ' . $e->getMessage()
            ], 404);
        }
    }

    // Thêm bình luận
    public function addComment(Request $request)
    {
        $comment = new TaskComment([
            'comment' => $request->comment,
            'user_id' => Auth::id(),
            'task_id' => $request->task_id,
            'updated_at' => null,
        ]);
        $comment->save();
        $comment->load(['user']);
        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment]);
    }
    // cập nhật bình luận
    public function updateComment(Request $request)
    {
        $comment = TaskComment::findOrFail($request->commentId);
        // Kiểm tra quyền sửa bình luận
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        // Cập nhật nội dung bình luận
        $comment->comment = $request->comment;
        $comment->save();
        return response()->json(['message' => 'Comment updated successfully']);
    }
    // Xóa bình luận
    public function deleteComment(Request $request)
    {
        try {
            $comment = TaskComment::findOrFail($request->commentId);
            $comment->delete();
            return response()->json([
                'message' => 'Đã xóa bình luận!'
            ], 200);
        } catch (\Exception $e) {
            // Trả về lỗi nếu không tìm thấy task hoặc có lỗi xảy ra
            return response()->json([
                'message' => 'Error deleting task: ' . $e->getMessage()
            ], 404);
        }
    }

    // Thêm attachment
    public function storeAttachment(Request $request, $taskId)
    {
        // Log::info($request->all());  // Sẽ ghi log toàn bộ yêu cầu đến
        $request->validate([
            'file' => 'required|file'
        ]);
        // Lấy tên tệp gốc
        $originalFilename = $request->file('file')->getClientOriginalName();
        // Tạo tên tệp duy nhất bằng cách sử dụng UUID và giữ lại phần mở rộng
        $filename = Str::uuid()->toString() . '.' . $request->file('file')->getClientOriginalExtension();
        // Lưu tệp vào thư mục chỉ định
        $path = $request->file('file')->storeAs('public/task_attachments', $filename);

        // $path = $request->file('file')->store('public/task_attachments');
        $attachment = new TaskAttachment([
            'task_id' => $taskId,
            'file_path' => $path,
            'filename' => $originalFilename, // Lưu tên tệp gốc
        ]);
        $attachment->save();
        // return response()->json(['message' => 'Tệp đã được tải lên thành công!', 'attachment' => $attachment]);
        return response()->json([
            'message' => 'Tệp đã được tải lên thành công!',
            'attachment' => [
                'id' => $attachment->id,
                'url' => Storage::url($path),
                'filename' => $originalFilename
            ]
        ]);
    }

    public function destroyAttachment($id)
    {
        $attachment = TaskAttachment::findOrFail($id);
        Storage::delete($attachment->file_path);
        $attachment->delete();
        return response()->json(['message' => 'Tệp đã được xóa thành công!']);
    }

    public function addTag(Request $request, $taskId)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $tag = Tag::firstOrCreate(['name' => $request->name]);
        $task = Task::findOrFail($taskId);
        $task->tags()->syncWithoutDetaching([$tag->id]);//thêm các mối quan hệ mới mà không xóa bất kỳ mối quan hệ hiện có

        return response()->json(['message' => 'Tag added successfully', 'tag' => $tag]);
    }

    public function destroyTag(Request $request, $taskId)
    {
        $request->validate([
            'tag_id' => 'required|integer|exists:tags,id'
        ]);

        $task = Task::findOrFail($taskId);
        $task->tags()->detach($request->tag_id);

        return response()->json(['message' => 'Tag removed successfully']);
    }

    public function addUser(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        $user = User::findOrFail($request->userId);
        // Kiểm tra nếu người dùng đã tồn tại trong task_users
        if ($task->users->contains($user->id)) {
            return response()->json(['success' => false, 'message' => 'Người dùng đã tồn tại trong công việc này.']);
        }
        // Thêm người dùng vào công việc với vai trò
        $task->users()->attach($user->id, ['role' => $request->role]);
        // Tải lại mối quan hệ pivot
        $userWithPivot = $task->users()->where('user_id', $user->id)->first();
        return response()->json(['success' => true, 'user' => $userWithPivot]);
    }
    public function removeUser($taskId, $userId)
    {
        $task = Task::findOrFail($taskId);
        $user = User::findOrFail($userId);
        // Kiểm tra nếu người dùng tồn tại trong task_users
        if (!$task->users->contains($user->id)) {
            return response()->json(['success' => false, 'message' => 'Người dùng không tồn tại trong công việc này.']);
        }
        // Xóa người dùng khỏi công việc
        $task->users()->detach($user->id);
        return response()->json(['success' => true]);
    }

    public function addOrder(Request $request)
    {
        $validated = $request->validate([
            'taskId' => 'required|exists:tasks,id',
            'orderNumber' => 'required|string'
        ]);
        $task = Task::findOrFail($validated['taskId']);
        $order = Order::where('order_code', $validated['orderNumber'])->first();
        if (!$task || !$order) {
            return response()->json(['message' => 'Task or Order not found'], 404);
        }
        $task->orders()->attach($order->id);
        return response()->json(['order' => $order]);
    }

    public function removeOrder($orderId, Request $request)
    {
        $validated = $request->validate([
            'taskId' => 'required|exists:tasks,id'
        ]);
        $task = Task::findOrFail($validated['taskId']);
        $order = Order::find($orderId);
        if (!$task || !$order) {
            return response()->json(['message' => 'Task or Order not found'], 404);
        }
        $task->orders()->detach($orderId);
        return response()->json(['message' => 'Order removed successfully'], 200);
    }

    public function addProduct(Request $request)
    {
        $validated = $request->validate([
            'taskId' => 'required|exists:tasks,id',
            'productNumber' => 'required|string'
        ]);

        $task = Task::findOrFail($validated['taskId']);
        $product = Product::where('sku', $validated['productNumber'])->first();

        if (!$task || !$product) {
            return response()->json(['message' => 'Task or Product not found'], 404);
        }

        $task->products()->attach($product->id);
        return response()->json(['product' => $product]);
    }

    public function removeProduct($productId, Request $request)
    {
        $validated = $request->validate([
            'taskId' => 'required|exists:tasks,id'
        ]);

        $task = Task::findOrFail($validated['taskId']);
        $product = Product::find($productId);

        if (!$task || !$product) {
            return response()->json(['message' => 'Task or Product not found'], 404);
        }

        $task->products()->detach($productId);
        return response()->json(['message' => 'Product removed successfully'], 200);
    }

    public function addCustomer(Request $request)
    {
        $validated = $request->validate([
            'taskId' => 'required|exists:tasks,id',
            'customerNumber' => 'required|string'
        ]);

        $task = Task::findOrFail($validated['taskId']);
        $customer = Customer::where('customer_code', $validated['customerNumber'])->first();

        if (!$task || !$customer) {
            return response()->json(['message' => 'Task or Customer not found'], 404);
        }

        $task->customers()->attach($customer->id);
        return response()->json(['customer' => $customer]);
    }

    public function removeCustomer($customerId, Request $request)
    {
        $validated = $request->validate([
            'taskId' => 'required|exists:tasks,id'
        ]);

        $task = Task::findOrFail($validated['taskId']);
        $customer = Customer::find($customerId);

        if (!$task || !$customer) {
            return response()->json(['message' => 'Task or Customer not found'], 404);
        }

        $task->customers()->detach($customerId);
        return response()->json(['message' => 'Customer removed successfully'], 200);
    }

    public function addRelatedTask(Request $request)
    {
        $validated = $request->validate([
            'taskId' => 'required|exists:tasks,id',
            'taskCode' => 'required|string|exists:tasks,task_code'
        ]);

        $task = Task::findOrFail($validated['taskId']);
        $relatedTask = Task::where('task_code', $validated['taskCode'])->first();

        if (!$task || !$relatedTask) {
            return response()->json(['message' => 'Task or Related Task not found'], 404);
        }

        $task->relatedTasks()->attach($relatedTask->id, ['relation_type' => 2]);
        return response()->json(['relatedTask' => $relatedTask]);
    }

    public function removeRelatedTask($taskId, Request $request)
    {
        $validated = $request->validate([
            'relatedTaskId' => 'required|exists:tasks,id'
        ]);

        $task = Task::findOrFail($taskId);
        $relatedTask = Task::findOrFail($validated['relatedTaskId']);

        if (!$task || !$relatedTask) {
            return response()->json(['message' => 'Task or Related Task not found'], 404);
        }

        DB::table('task_relations')
            ->where('task_id', $task->id)
            ->where('related_task_id', $relatedTask->id)
            ->delete();

        return response()->json(['message' => 'Related task removed successfully'], 200);
    }


}
