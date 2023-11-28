<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Message extends Model
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = [
        'sender',
        'reciver',
        'message',
        'subject',
        'recived_time',
    ];

    public static $vlidations_roles = [
        "sender" => "required",
        "reciver" => "required",
        "message" => "required|string",
        "subject" => "required|string",
    ];

    
}
