<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected  $guarded = ['id'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'offers_products', 'offer_id', 'product_id');
    }

    public static function activeOffers()
    {
        return self::where('start_date', '<=', \Carbon\Carbon::now())->where('end_date', '>=', \Carbon\Carbon::now())->where('status', 1)->get();
    }

    public function getOfferTypeAttribute()
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
