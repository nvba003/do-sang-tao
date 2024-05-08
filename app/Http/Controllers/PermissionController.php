<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    // Hiển thị danh sách quyền
    public function index()
    {
        $permissions = Permission::all();
        $header = "Danh Sách Quyền"; // Đây là giá trị của $header bạn muốn hiển thị
        return view('permissions.index', compact('permissions', 'header'));
    }

    // Hiển thị form tạo quyền mới
    public function create()
    {
        $header = "Tạo Mới Quyền";
        return view('permissions.create', compact('header'));
    }

    // Lưu quyền mới
    public function store(Request $request)
    {
        Permission::create(['name' => $request->name]);
        return redirect()->route('permissions.index')->with('success', 'Permission created successfully');
    }

    // Hiển thị form chỉnh sửa quyền
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    // Cập nhật quyền
    public function update(Request $request, Permission $permission)
    {
        $permission->update(['name' => $request->name]);
        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully');
    }

    // Xoá quyền
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully');
    }
}
