<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'img_url',
        'email',
        'password',
        'post',
        'address',
        'building'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile(){
        return $this->hasOne('App\Models\Profile');
    }

    public function items(){
        return $this->belongsToMany(Item::class, 'purchases')->withTimestamps();
    }

    public function comments(){
        return $this->belongsToMany(Item::class, 'comments')->withPivot('comment')->withTimestamps();
    }

    public function like(){
        return $this->belongsToMany(Item::class, 'item_user_like')->withTimestamps();
    }

}
