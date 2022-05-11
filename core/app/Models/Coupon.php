<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected  $guarded = ['id'];

    public function appliedCoupons()
    {
        return $this->hasMany(AppliedCoupon::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'coupons_categories', 'coupon_id', 'category_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupons_products', 'coupon_id', 'product_id');
    }

    public function getCouponTypeAttribute()
    {
        if($this->discount_type == 1){
            return 'Fixed';
        }else{

            return 'Percentage';
        }
    }

    public function getStatusTextAttribute()
    {
        if($this->status == 1){
            return 'Active';
        }else{

            return 'Deactivated';
        }
    }
}
