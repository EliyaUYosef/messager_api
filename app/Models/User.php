<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'email_verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public static $register_form_rules = [
        "name" => "required|string|max:255|min:2",
        "email" => "required|email|unique:users|max:255|min:6",
        "password" => "required|string|confirmed|min:8|max:50|regex:/[^\d]/"
    ];

    /**
     * variable
     *
     * @var array $login_rules
     * 
     * desc:  validation rules for field are 
     *        exist on login form.
     */
    public static $login_form_rules = [
        "email" => "required|email|max:255|min:6",
        "password" => "required|string|min:8|max:50|regex:/[^\d]/"
    ];

    public static $validations_id_rules = [
        "user_id" => "required|integer|min:1",
    ];

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'reciver');
    }
}