<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
    protected $casts = [
        'products' => 'json',
     ];

     protected $primaryKey = 'uuid';
    protected $keyType = "string";
}
