<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MessageService 
{
    public function check_if_message_exist($message_id): bool 
    {
        $message = Message::find($message_id);
        
        return !empty($message);
    }

    public function create_message($message_object) {
        return Message::create($message_object);
    }
    public function get_unread_messages_by_user_id($user_id) {
        return Message::where('reciver', $user_id)
            ->where('recieved_flag', NULL)
            ->orderBy('id', 'DESC')
            ->get();

        // return User::find($user_id)
        //                  ->receivedMessages()
        //                  ->where('recieved_flag', null)
        //                  ->orderBy('id', 'DESC')
        //                  ->get();
    }
    public function get_messages_for_user() {
        $user = Auth::user();
        // return Message::where('reciver', $user->id)
        //             ->orderBy('id', 'DESC')
        //             ->get();

        return User::find( $user->id)
                         ->receivedMessages()
                         ->orderBy('id', 'DESC')
                         ->get();
    }
    public function delete_message_by_id($message_id) {
        return Message::where('id', $message_id)->delete();
    }
    public function mark_as_read($message_id) {
        return Message::where('id', $message_id)->update(['recieved_flag' => now()]);;
    }
    public function get_message_by_id($message_id) {
        return Message::find($message_id);
    }
    public function get_message_validations_rules() {
        return Message::$validations_rules;
    }
    public function get_delete_message_req_validations() {
        return Message::$validations_rules_for_delete;
    }
}
