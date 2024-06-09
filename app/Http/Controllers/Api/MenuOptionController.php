<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContainerMenuOption;
use App\Models\Category;

class MenuOptionController extends Controller
{
    // public function getChildren($parentId)
    // {
    //     $children = ContainerMenuOption::where('parent_id', $parentId)->get();
    //     return response()->json($children);
    // }
    public function getChildren($parentId)
    {
        $children = Category::where('parent_id', $parentId)->get();
        return response()->json($children);
    }
}
