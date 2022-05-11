<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellLog extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class,'seller_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::created(function($log)
        {
            if($log->seller_id != 0){
                $trx = new Transaction();
                $trx->seller_id = $log->seller_id;
                $trx->amount = $log->after_commission;
                $trx->charge = 0;
                $trx->post_balance = $log->seller->balance;
                $trx->trx_type = '+';
                $trx->trx = getTrx();
                $trx->details = 'Product sell profit has been added';
                $trx->save();
            }
        });
    }
}
