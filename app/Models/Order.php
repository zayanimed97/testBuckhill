<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;
    use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

    protected $primaryKey = 'uuid';
    protected $keyType = "string";

    protected $casts = [
        'products' => 'json',
        'address' => 'json'
    ];

     
    public function status()
    {
        return $this->hasOne(OrderStatus::class, 'id', 'order_status_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'id', 'payment_id');
    }
    
    public function products_relation()
    {
       return $this->BelongsToJson(Product::class , 'products[]->uuid');
    }
}
