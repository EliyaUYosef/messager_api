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

    public static $validations_rules = [
        "sender" => "required|integer|min:1",
        "reciver" => "required|integer",
        "message" => "required|string|max:20000",
        "subject" => "required|string|max:255|min:3",
    ];

    public function sender() {
        return $this->belongsTo(User::class,"sender",'id');
    }    
    public function reciver() {
        return $this->belongsTo(User::class,"reciver",'id');
    }    
}
