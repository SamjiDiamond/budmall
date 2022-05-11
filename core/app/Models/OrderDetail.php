<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }


    public function scopeOrders()
    {
        return $this->whereHas('order',function($q){
            $q->where('payment_status',  '!=' ,0)->when(request()->search,function($order){
                return $order->where('order_number',request()->search);
            });
         });

    }
    
    public function scopePendingOrder()
    {
        return $this->whereHas('order',function($q){
            $q->where('payment_status', '!=' , 0)->where('status', 0)->when(request()->search,function($order){
                return $order->where('order_number',request()->search);
            });
         });

    }

    public function scopeProcessingOrder()
    {
        return $this->whereHas('order',function($q){
            $q->where('payment_status', '!=' , 0)->where('status', 1)->when(request()->search,function($order){
                return $order->where('order_number',request()->search);
            });
        });

    }

    public function scopeDispatchedOrder()
    {
        return $this->whereHas('order',function($q){
            $q->where('payment_status', '!=' , 0)->where('status', 2)->when(request()->search,function($order){
                return $order->where('order_number',request()->search);
            });
         });

    }
    public function scopeDeliveredOrder()
    {
        return $this->whereHas('order',function($q){
            $q->where('payment_status', '!=' , 0)->where('status', 3)->when(request()->search,function($order){
                return $order->where('order_number',request()->search);
            });
         });

    }
    public function scopeCancelledOrder()
    {
        return $this->whereHas('order',function($q){
            $q->where('payment_status', '!=' , 0)->where('status', 4)->when(request()->search,function($order){
                return $order->where('order_number',request()->search);
            });
         });

    }

    public function scopeCod()
    {
        return $this->whereHas('order',function($q){
            $q->where('payment_status',2);
        });

    }

   
}
