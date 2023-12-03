<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'recieved_flag',
    ];

    public static $validations_rules = [
        "reciver" => "required|integer|min:1",
        "message" => "required|string|max:20000",
        "subject" => "required|string|max:255|min:3",
    ];

    public static $validations_id_rules = [
        "message_id" => "required|integer|min:1",
    ];

    public function sender() : BelongsTo 
    {
        return $this->belongsTo(User::class,"sender",'id');
    }    

    public function reciver() : BelongsTo  
    {
        return $this->belongsTo(User::class,"reciver",'id');
    }    
}
