<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    //protected $primaryKey = 'product_api_id';
    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function productGroup()
    {
        return $this->belongsTo(ProductGroup::class, 'product_group_id');
    }

    public function containers()
    {
        return $this->hasMany(Container::class, 'product_id', 'product_api_id');
    }

    public function bundles()
    {
        return $this->hasMany(BundleItem::class, 'bundle_id', 'bundle_id');
    }

    public function bundle()
    {
        return $this->hasOne(Bundle::class, 'id', 'bundle_id');
    }

    public function supplierProducts()
    {
        return $this->hasMany(SupplierProduct::class, 'product_api_id', 'product_api_id');
    }

    public function supplierLinks()
    {
        return $this->hasMany(ProductSupplierLink::class, 'product_api_id', 'product_api_id');
    }

    public function purchaseProducts()
    {
        return $this->hasMany(PurchaseProduct::class, 'product_api_id', 'product_api_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_api_id', 'product_api_id');
    }
}
