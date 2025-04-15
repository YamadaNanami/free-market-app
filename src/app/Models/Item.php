<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'categories',
        'img_url',
        'item_name',
        'brand_name',
        'price',
        'description',
        'condition'
    ];

    public function categories(){
        return $this->belongsToMany(Category::class, 'category_item')->withTimestamps();
    }
    public function users(){
        return $this->belongsToMany(User::class, 'purchases')->withTimestamps();
    }
}
