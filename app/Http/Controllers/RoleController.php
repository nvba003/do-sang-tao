<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    // Hiển thị danh sách vai trò
    public function index()
    {
        $roles = Role::all();
        $header = "Danh Sách Vai Trò";
        return view('roles.index', compact('roles','header'));
    }

    // Hiển thị form tạo vai trò mới
    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    // Lưu vai trò mới
    public function store(Request $request)
    {
        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Role created successfully');
    }

    // Hiển thị form chỉnh sửa vai trò
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('roles.edit', compact('role', 'permissions'));
    }

    // Cập nhật vai trò
    public function update(Request $request, Role $role)
    {
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully');
    }

    // Xoá vai trò
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}
