<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         // Xóa dữ liệu hiện có để tránh trùng lặp
         Task::truncate();

         // Thêm dữ liệu mới
         Task::create(['name' => 'Task 1', 'description' => 'Mô tả task 1', 'due_date' => '2023-01-01 00:00:00', 'status' => 'pending']);
         Task::create(['name' => 'Task 2', 'description' => 'Mô tả task 2', 'due_date' => '2023-01-02 00:00:00', 'status' => 'completed']);
 
         // Thêm nhiều tasks hơn nếu cần
    }
}
