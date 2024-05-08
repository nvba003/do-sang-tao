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

    public function prepareEdit(Request $request)
    {
        // Lấy ID từ request và lưu vào session
        if ($request->has('id')) {
            session(['edit_user_id' => $request->id]);
        }
        // Chuyển hướng tới route edit
        return redirect()->route('users.edit');
    }

    public function edit(Request $request)
    {
        // if ($request->has('id')) { // session đã được lưu nên không cần tạo nữa
        //     session(['edit_user_id' => $request->id]);
        // }
        // Lấy ID từ session
        $id = session('edit_user_id');
        $user = User::findOrFail($id);
        $roles = Role::all();
        $permissions = Permission::all();
        $header = 'Chỉnh sửa vai trò người dùng';
        return view('users.edit', compact('user', 'roles', 'permissions', 'header'));
    }

    public function update(Request $request, User $user)
    {
        // $user->roles()->detach();// Xóa tất cả vai trò hiện có

        if ($request->has('id')) {
            session(['edit_user_id' => $request->id]);
        }
        // Lấy ID từ session
        $id = session('edit_user_id');
        $user = User::findOrFail($id);
        // Validate các dữ liệu đầu vào nếu cần
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id', // Đảm bảo các ID role tồn tại trong bảng roles
        ]);
        $roleNames = Role::whereIn('id', $request->roles)->pluck('name')->toArray();// Lấy tên các roles từ ID
        $user->syncRoles($roleNames);// Đồng bộ hóa vai trò cho người dùng
        // Kiểm tra nếu có permissions trong request
        // if ($request->has('permissions')) {
        //     $request->validate([
        //         'permissions' => 'nullable|array',
        //         'permissions.*' => 'exists:permissions,id', // Đảm bảo các ID permission tồn tại trong bảng permissions
        //     ]);
        //     $user->syncPermissions($request->permissions);
        // } else {
        //     $user->syncPermissions([]); // Xóa hết permissions nếu không có permission nào được gửi
        // }
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function create()
    {
        return view('users.create',['header' => 'Tạo mới user']);
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        //dd($validatedData);
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), // Mã hóa mật khẩu trước khi lưu
        ]);
        // Gán vai trò "user" cho người dùng mới
        $user->assignRole('user');
        return redirect()->route('users.index')->with('success', 'Người dùng mới đã được thêm thành công.');
    }

    public function destroy(User $user)
    {
        // Xóa người dùng
        $user->delete();
        // Chuyển hướng về trang danh sách người dùng với thông báo thành công
        return redirect()->route('users.index')->with('success', 'Người dùng đã được xóa thành công.');
    }

}
