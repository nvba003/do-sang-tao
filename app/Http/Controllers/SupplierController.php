<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use App\Models\Product;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with('group')->get();
        return view('suppliers.index', compact('suppliers'),['header' => 'Nhà cung cấp']);
    }

    public function create()
    {
        $groups = SupplierGroup::all();
        return view('suppliers.create', compact('groups'), ['header' => 'Thêm nhà cung cấp']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string',
            'average_rating' => 'nullable|numeric|min:0|max:5',
            'link' => 'nullable|url',
            'supplier_group_id' => 'required|exists:supplier_groups,id',
        ]);

        Supplier::create($request->all());

        return redirect()->route('suppliers.index');
    }

    public function edit(Supplier $supplier)
    {
        $groups = SupplierGroup::all();
        return view('suppliers.edit', compact('supplier', 'groups'), ['header' => 'Chỉnh sửa nhà cung cấp']);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string',
            'average_rating' => 'nullable|numeric|min:0|max:5',
            'link' => 'nullable|url',
            'supplier_group_id' => 'required|exists:supplier_groups,id',
        ]);

        $supplier->update($request->all());

        return redirect()->route('suppliers.index');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index');
    }
}
