<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // // Tạo quyền
        // $permission1 = Permission::create(['name' => 'edit posts']);
        // $permission2 = Permission::create(['name' => 'delete posts']);
        // // Tạo vai trò và gán quyền
        // $role = Role::create(['name' => 'editor']);
        // $role->givePermissionTo($permission1);
        // $role->givePermissionTo($permission2);
        // // Hoặc gán nhiều quyền cùng một lúc
        // //$role->syncPermissions([$permission1, $permission2]);

        // Tạo quyền
        $createPosts = Permission::create(['name' => 'create posts']);
        $editPosts = Permission::create(['name' => 'edit posts']);
        $deletePosts = Permission::create(['name' => 'delete posts']);
        $approveComments = Permission::create(['name' => 'approve comments']);
        $deleteComments = Permission::create(['name' => 'delete comments']);
        $manageUsers = Permission::create(['name' => 'manage users']);

        // Tạo vai trò Admin và gán tất cả quyền
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Tạo vai trò Editor và gán quyền liên quan đến bài viết
        $editor = Role::create(['name' => 'editor']);
        $editor->givePermissionTo($createPosts, $editPosts, $deletePosts);

        // Tạo vai trò Moderator và gán quyền liên quan đến bình luận
        $moderator = Role::create(['name' => 'moderator']);
        $moderator->givePermissionTo($approveComments, $deleteComments);

        // Tạo vai trò User với quyền hạn giới hạn
        $user = Role::create(['name' => 'user']);
        // Có thể không cần gán quyền cụ thể nếu User chỉ có quyền cơ bản
    }
}
