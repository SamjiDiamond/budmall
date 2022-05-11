<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function assignAttributes()
    {
        return $this->hasMany(AssignProductAttribute::class, 'product_attribute_id');
    }
}
