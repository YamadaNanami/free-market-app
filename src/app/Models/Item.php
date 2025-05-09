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

    public function comments(){
        return $this->belongsToMany(User::class,'comments')->withPivot('comment')->withTimestamps();
    }

    public function like(){
        return $this->belongsToMany(User::class, 'item_user_like')->withTimestamps();
    }

    public function scopeItemsSearch($query,$keyword){
        if(!empty($keyword)){
            $query->where('item_name', 'like', '%' . $keyword . '%');
        }
    }

}
