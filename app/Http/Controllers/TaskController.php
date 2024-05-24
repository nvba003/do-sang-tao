<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task\Task;
use App\Models\Task\TaskCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $categories = TaskCategory::all();
        $users = User::all();
        $query = Task::with('users'); // Đảm bảo rằng thông tin người dùng được nạp để tránh N+1 queries

        // Lọc tasks dựa trên category_id nếu có
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc tasks trong vòng 30 ngày gần nhất
        $query->where('created_at', '>=', Carbon::now()->subDays(30));

        // Chỉ hiển thị tasks liên quan đến người dùng hiện tại hoặc tất cả nếu là admin
        if (!Auth::user()->is_admin) {
            $query->whereHas('users', function ($q) {
                $q->where('id', Auth::id());
            });
        }

        $tasks = $query->get()->groupBy('status');
        $statuses = [
            1 => 'Chưa bắt đầu',
            2 => 'Đang làm',
            3 => 'Đợi xử lý',
            4 => 'Hoàn thành'
        ];

        return view('tasks.task', compact('tasks', 'statuses', 'categories', 'users'), ['header' => 'Quản lý công việc']);
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
            'category_id' => $request->category_id
        ]);
        // dd($task);
        // Save the task to get an ID
        $task->save();
        
        // Retrieve the category to get the first letter of the name
        $category = TaskCategory::find($request->category_id);
        $firstLetter = strtoupper(substr($category->name, 0, 1));

        // Generate task_code based on the first letter of the category name and the task id
        $task->task_code = $firstLetter . sprintf("%04d", $task->id); // Padding the ID to ensure it has at least 4 digits

        // Save the task again to update the task_code
        $task->save();

        return redirect()->route('tasks.index')->with('success', 'Task created successfully with Task Code: ' . $task->task_code);
    }

    // Hiển thị chi tiết task
    public function show($id)
    {
        $task = Task::with(['comments', 'attachments', 'customers'])->findOrFail($id);
        return view('tasks.task_detail', compact('task'), ['header' => 'Chi tiết công việc']);
    }

    // Thêm bình luận
    public function addComment(Request $request, $taskId)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $comment = new Comment([
            'body' => $request->body,
            'user_id' => Auth::id(),
            'task_id' => $taskId
        ]);
        $comment->save();

        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment]);
    }

    // Thêm attachment
    public function addAttachment(Request $request, $taskId)
    {
        $request->validate([
            'attachment' => 'required|file',
        ]);

        $path = $request->file('attachment')->store('attachments');

        $attachment = new Attachment([
            'path' => $path,
            'task_id' => $taskId
        ]);
        $attachment->save();

        return response()->json(['message' => 'Attachment uploaded successfully', 'attachment' => $attachment]);
    }

    // Thêm tag
    public function addTag(Request $request, $taskId)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $task = Task::findOrFail($taskId);
        $tag = $task->tags()->create(['name' => $request->name]);

        return response()->json(['message' => 'Tag added successfully', 'tag' => $tag]);
    }
}
