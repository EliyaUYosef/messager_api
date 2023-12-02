<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class MessageService
{
    // Define time intervals in seconds
    private $intervals_time = array(
        31536000 => array('singular' => 'year', 'plural' => 'years'),
        2592000 => array('singular' => 'month', 'plural' => 'months'),
        604800 => array('singular' => 'week', 'plural' => 'weeks'),
        86400 => array('singular' => 'day', 'plural' => 'days'),
        3600 => array('singular' => 'hour', 'plural' => 'hours'),
        60 => array('singular' => 'minute', 'plural' => 'minutes'),
        1 => array('singular' => 'second', 'plural' => 'seconds'),
    ); 
    /**
     * Return bool if message exist in DB
     *
     * @param integer $message_id
     * @return boolean
     */
    public function check_if_message_exist(int $message_id): bool
    {
        $message = Message::find($message_id);

        return !empty($message);
    }

    /**
     * Insert new message to DB
     *
     * @param array $message
     * @return Message
     */
    public function create_message(array $message): Message
    {
        return Message::create($message);
    }

    /**
     * Get chat messages
     *
     * @param integer $user_id
     * @param integer $reciver_id
     * @return array
     */
    public function get_messages_from_specific_user(int $user_id, int $reciver_id): LengthAwarePaginator
    {
        return Message::where('sender', $user_id)
            ->where('reciver', $reciver_id)
            ->orWhere(function ($query) use ($user_id, $reciver_id) {
                $query->where('sender', $reciver_id)->where('reciver', $user_id);
            })
            ->orderBy('id', 'ASC')
            ->paginate(6);
    }

    /**
     * Get unread messages for chat
     *
     * @param integer $user_id
     * @param integer $reciver_id
     * @return array
     */
    public function get_unread_messages_from_specific_user(int $user_id, int $reciver_id): LengthAwarePaginator
    {
        return Message::where(function ($query) use ($user_id, $reciver_id) {
            $query->where('sender', $user_id)->where('reciver', $reciver_id);
        })->orWhere(function ($query) use ($user_id, $reciver_id) {
            $query->where('sender', $reciver_id)->where('reciver', $user_id);
        })
            ->where('recieved_flag', 0)
            ->orderBy('id', 'ASC')
            ->paginate(6);
    }

    /**
     * Remove message from DB
     *
     * @param integer $message_id
     * @return integer
     */
    public function delete_message_by_id(int $message_id): int
    {
        return Message::where('id', $message_id)
            ->delete();
    }

    /**
     * Update message row as already read
     *
     * @param integer $message_id
     * @return boolean
     */
    public function mark_as_read(int $message_id): bool
    {
        return Message::where('id', $message_id)
            ->update(['recieved_flag' => 1]);;
    }

    /**
     * Find specific message by id
     *
     * @param integer $message_id
     * @return Message
     */
    public function get_message_by_id(int $message_id): ?Message
    {
        return Message::find($message_id);
    }

    /**
     * Check if the specified user (receiver) 
     *  exists in the recipients of a message.
     *
     * @param integer $message_id
     * @param integer $user_id
     * @return boolean
     */
    public function check_if_receiver_exist(int $message_id, int $user_id): ?Message
    {
        return Message::find($message_id);
    }

    // Validations rules functions - - - - - - - - - - -

    /**
     * Return validations rules array for create message
     *
     * @return array
     */
    public function message_validations_rules(): array
    {
        return Message::$validations_rules;
    }

    /**
     * Return validations rules array for message_id req
     *
     * @return array
     */
    public function message_id_validations(): array
    {
        return Message::$validations_id_rules;
    }
}
