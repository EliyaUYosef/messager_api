<?php

namespace App\Services;

use App\Models\Message;

class MessageService 
{
    public function check_if_message_exist($message_id, $field = '', $value = ''): bool 
    {
        if ($field !== '') {
            $message = Message::where('id', $message_id)
                ->where($field, $value)->first();
        }
        else {
            $message = Message::where('id', $message_id)->first();
        }
        
        return $message ? true : false ;
    }

    public function create_message($message_object) {
        return Message::create($message_object);
    }
    public function get_unread_messages_by_user_id($user_id) {
        return Message::where('reciver', $user_id)
            ->where('recived_time', NULL)
            ->orderBy('id', 'DESC')
            ->get();
    }
    public function delete_message_by_id($message_id) {
        return Message::where('id', $message_id)->delete();
    }
    public function mark_as_read($message_id) {
        return Message::where('id', $message_id)->update(['recived_time' => now()]);;
    }
    public function get_message_by_id($message_id) {
        return Message::find($message_id);
    }
    public function get_message_validations_rules() {
        return Message::$validations_rules;
    }
}
