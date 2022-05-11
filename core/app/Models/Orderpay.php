<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orderpay extends Model
{
    protected $table = 'orderpays';
    protected $guarded = ['id'];

    protected $casts = [
        'detail' => 'object'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
    public function gateway()
    {
        return $this->belongsTo(Gateway::class, 'method_code', 'code');
    }

    // scope
    public function scopegatewayCurrency()
    {
        return GatewayCurrency::where('method_code', $this->method_code)->where('currency', $this->method_currency)->first();
    }

    public function scopeBaseCurrency()
    {
        return $this->gateway->crypto == 1 ? 'USD' : $this->method_currency;
    }

    public function scopePending()
    {
        return $this->where('status', 2);
    }
}