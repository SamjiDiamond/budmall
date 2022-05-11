<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Seller extends Authenticatable
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'ver_code_send_at' => 'datetime'
    ];

    public function getFullnameAttribute()
    {
       return $this->firstname.' '.$this->lastname;
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function loginLogs()
    {
        return $this->hasMany(SellerLogin::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class,'seller_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class,'seller_id')->orderBy('id','desc');
    }
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class,'seller_id')->where('status','!=',0);
    }

    public function scopeActive()
    {
        return $this->where('status',1);
    }
    public function scopeBanned()
    {
        return $this->where('status',0);
    }
    public function scopeFeatured()
    {
        return $this->where('featured',1);
    }

    public function scopeEmailUnverified()
    {
        return $this->where('ev', 0);
    }

    public function scopeSmsUnverified()
    {
        return $this->where('sv', 0);
    }
    public function scopeEmailVerified()
    {
        return $this->where('ev', 1);
    }

    public function scopeSmsVerified()
    {
        return $this->where('sv', 1);
    }

    public function totalSold()
    {
        return $this->products()->sum('sold');
    }
}
