<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_action_time'
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
        'password' => 'hashed',
    ];

    public static $register_rules = [
        "name" => "required|string|max:255|min:2",
        "email" => "required|email|unique:users|max:255|min:6",
        "password" => "required|string|confirmed|min:8|max:50"
    ];
    public static $login_rules = [
        "email" => "required|email|unique:users|max:255|min:6",
        "password" => "required|string|confirmed|min:8|max:50"
    ];

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver');
    }
}