<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class HomeController extends Controller
{
    public function index()
    {
        // Logic để lấy dữ liệu từ Model (nếu cần)
        // Ví dụ: $posts = Post::all();
        $tasks = Task::all(); // Lấy tất cả tasks
        return view('index', ['tasks' => $tasks]);
        //return view('index'); // Trả về view `index` và gửi dữ liệu tới view nếu cần
    }
}
