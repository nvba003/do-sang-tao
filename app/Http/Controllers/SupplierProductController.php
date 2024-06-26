<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\Product;

class SupplierProductController extends Controller
{
    public function index()
    {
        $products = Product::with('supplierProducts')->get();
        // $supplierProducts = SupplierProduct::with('supplier', 'product')->get();
        return view('supplier_products.index', compact('products'))->with('header', 'Tất cả sản phẩm của nhà cung cấp');
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('supplier_products.create', compact('suppliers', 'products'),['header' => 'Thêm sản phẩm nhà cung cấp']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_api_id' => 'required|exists:products,product_api_id',
            'supplier_id' => 'required|exists:suppliers,id',
            'supplier_product_id' => 'required|string',
            'supplier_product_url' => 'required|url',
            'available' => 'required|boolean',
        ]);

        SupplierProduct::create([
            'product_api_id' => $request->product_api_id,
            'supplier_id' => $request->supplier_id,
            'supplier_product_id' => $request->supplier_product_id,
            'supplier_product_url' => $request->supplier_product_url,
            'available' => $request->available,
        ]);

        return redirect()->route('supplier-products.create')->with('success', 'Sản phẩm đã được thêm thành công!');
    }

    public function edit($supplierId, $id)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $product = SupplierProduct::findOrFail($id);
        return view('supplier_products.edit', compact('supplier', 'product'),['header' => 'Sửa sản phẩm nhà cung cấp']);
    }

    public function update(Request $request, $supplierId, $id)
    {
        $request->validate([
            'product_api_id' => 'required|exists:products,product_api_id',
            'supplier_product_id' => 'required|string',
            'supplier_product_url' => 'required|url',
            'available' => 'required|boolean',
        ]);

        $product = SupplierProduct::findOrFail($id);
        $product->update($request->all());

        return redirect()->route('supplier_products.index', $supplierId);
    }

    public function destroy($supplierId, $id)
    {
        $product = SupplierProduct::findOrFail($id);
        $product->delete();

        return redirect()->route('supplier_products.index', $supplierId);
    }
}
