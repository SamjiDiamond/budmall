<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $guarded  = ['id'];
    protected $casts = [
        'meta_keywords' => 'array'
    ];

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('name');
    }

    public function allSubcategories()
    {
        return $this->subcategories()->with('allSubcategories');
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupons_categories', 'category_id', 'coupon_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_categories', 'category_id', 'product_id');
    }

    public function products2()
    {
        return $this->belongsToMany(Product::class, 'products_categories', 'category_id', 'product_id', 'sub_id');
    }


    public function specialProuducts()
    {
        return $this->belongsToMany(Product::class, 'products_categories', 'category_id', 'product_id')->with('offer', 'offer.activeOffer', 'reviews')
        ->whereHas('categories')
        ->whereHas('brand')->orderBy('id', 'desc')->limit(15);
    }
}
