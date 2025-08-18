<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_user_id',
        'purchaser_user_id',
        'item_id'
    ];


    public function seller(){
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function purchaser(){
        return $this->belongsTo(User::class, 'purchaser_user_id');
    }

    public function chats(){
        return $this->hasMany(Chat::class);
    }

    public function item(){
        return $this->belongsTo(Item::class);
    }
}
