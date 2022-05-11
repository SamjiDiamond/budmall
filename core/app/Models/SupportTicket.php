<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $guarded = ['id'];

    public function getFullnameAttribute()
    {
        return $this->name;
    }

    public function getUsernameAttribute()
    {
        return $this->email;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function seller()
    {
        return $this->belongsTo(Seller::class,'seller_id');
    }

    public function supportMessage(){
        return $this->hasMany(SupportMessage::class);
    }

    public function statusBadge()
    {
        if($this->status == 0) return makeHtmlElement('span', 'success', 'Open');
        elseif($this->status == 1) return makeHtmlElement('span', 'primary', 'Answered');
        elseif($this->status == 2) return makeHtmlElement('span', 'warning', 'Replied');
        else return makeHtmlElement('span', 'dark', 'Closed');
    }

    public function priorityBadge()
    {
        if($this->status == 1) return makeHtmlElement('span', 'dark', 'Low');
        elseif($this->status == 2) return makeHtmlElement('span', 'warning', 'Medium');
        else return makeHtmlElement('span', 'danger', 'High');
    }
}
