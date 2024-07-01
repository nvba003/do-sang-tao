<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\Product;
use App\Models\ProductSupplierLink;
use App\Models\ProductGroup;

class SupplierProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage');
        $suppliers = Supplier::all();
        $productGroups = ProductGroup::all();
        $query = Product::query()
            ->when($request->filled('searchProductCode'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('searchProductCode') . '%')
                ->orWhere('sku', 'like', '%' . $request->input('searchProductCode') . '%');
            })
            ->when($request->filled('group'), function ($q) use ($request) {
                $q->where('product_group_id', $request->input('group'));
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = $request->input('status');
                $q->where(function ($subQuery) {
                    $subQuery->where('sku', 'like', 'G4%')
                             ->orWhere('sku', 'like', 'COMBO%');
                });
                if ($status == 0) {
                    $q->whereNull('bundle_id');
                } elseif ($status == 1) {
                    $q->whereNotNull('bundle_id');
                }
            })
            ->with(['supplierLinks.supplier.group', 'supplierLinks.supplierProduct.supplierSkus', 'supplierLinks.supplierProductSku'])
            ->orderBy('updated_at', 'asc');
        $products = $query->paginate($perPage);
        if ($request->ajax()) {
            return response()->json([
                'products' => $products->items(),
                'links' => $products->links('vendor.pagination.custom-tailwind')->toHtml(),
            ]);
        } else {
            // For initial page load, send JSON as part of the rendered page
            $initialData = json_encode([
                'products' => $products->items(),
                'links' => $products->links('vendor.pagination.custom-tailwind')->toHtml(),
            ]);
            return view('supplier_products.index', compact(
                'suppliers',
                'productGroups',
                'initialData'),
                ['header' => 'Tất cả sản phẩm của nhà cung cấp']
            );         
        }
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

    public function storeSupplierLink(Request $request)//cần xem lại
    {
        $request->validate([
            'product_api_id' => 'required|integer|exists:products,product_api_id',
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'provider' => 'required|string',
            'url' => 'required|url',
            'product_id' => 'required|string'
        ]);

        // Extract product_id from URL
        $product_id = $this->extractProductId($request->url);

        // Fetch product data using RapidAPIService
        $data = app('App\Http\Controllers\RapidController')->fetchData($request);

        if ($data->status() !== 200) {
            return response()->json(['success' => false, 'message' => 'Could not fetch product data'], 500);
        }

        $productData = $data->getData();

        // Create or update supplier link
        $supplierLink = ProductSupplierLink::updateOrCreate(
            [
                'product_api_id' => $request->product_api_id,
                'supplier_id' => $request->supplier_id
            ],
            [
                'supplier_product_id' => $productData->id,
                'supplier_product_sku_id' => isset($productData->supplier_product_skus) && count($productData->supplier_product_skus) === 1 ? $productData->supplier_product_skus[0]->id : null,
                'url' => $request->url
            ]
        );

        return response()->json(['success' => true, 'data' => $supplierLink]);
    }

    public function updateSupplierLink(Request $request, $id)
    {
        // $request->validate([
        //     'supplier_id' => 'required|integer|exists:suppliers,id',
        //     'url' => 'required|url',
        // ]);
        $supplierLink = ProductSupplierLink::findOrFail($id);
        // Update supplier link
        $supplierLink->update([
            'supplier_id' => $request->supplier_id,
            'supplier_product_id' => $request->supplier_product_id,
            'supplier_product_sku_id' => $request->supplier_product_sku_id,
            'supplier_group_id' => $request->supplier_group_id,
            'url' => $request->url,
            'available' => $request->available
        ]);
        return response()->json(['success' => true, 'data' => $supplierLink]);
    }

    public function destroySupplierLink($id)
    {
        $supplierLink = ProductSupplierLink::findOrFail($id);
        $supplierLink->delete();

        return response()->json(['success' => true]);
    }
}
