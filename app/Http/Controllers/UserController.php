<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all(); // Lấy tất cả người dùng
        $header = 'User';
        return view('users.index', compact('users', 'header')); // Truyền người dùng vào view
    }

    public function edit(User $user)
    {
        $roles = Role::all(); // Lấy tất cả vai trò
        $permissions = Permission::all(); // Lấy tất cả quyền
        $header = "Chỉnh sửa user";
        return view('users.edit', compact('user', 'roles', 'permissions', 'header'));
    }

    public function update(Request $request, User $user)
    {
        $roleIds = $request->roles; // Mảng ID vai trò từ form

        // Xóa tất cả vai trò hiện có
        $user->roles()->detach();

        // Gán lại vai trò dựa trên ID
        foreach ($roleIds as $roleId) {
            $role = Role::findById($roleId);
            $user->assignRole($role->name);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function create()
    {
        return view('users.create',['header' => 'Tạo mới user']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Mã hóa mật khẩu trước khi lưu
        ]);

        return redirect()->route('users.index')->with('success', 'Người dùng mới đã được thêm thành công.');
    }

}
