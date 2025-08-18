<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trade_id',
        'message',
        'img_url',
        'unread_flag'
    ];

    public function trade(){
        return $this->belongsTo(Trade::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
