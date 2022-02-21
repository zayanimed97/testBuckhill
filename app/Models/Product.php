<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
    protected $casts = [
        'metadata' => 'json',
     ];

    protected $primaryKey = 'uuid';
    protected $keyType = "string";

    public function category()
    {
        return $this->hasOne(Category::class, 'uuid', 'category_uuid');
    }

    public function brand()
    {
       return $this->BelongsToJson(Brand::class , 'metadata->brand', 'uuid');
    }
}
