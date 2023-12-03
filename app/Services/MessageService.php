<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class MessageService
{
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
        $message = Message::find($message_id);

        if ($message) {
            return $message;
        }

        return null;
    }

    /**
     * Check if the specified user (receiver) 
     *  exists in the recipients of a message.
     *
     * @param integer $message_id
     * @param integer $user_id
     * @return Message|null
     */
    public function check_if_receiver_exist(int $message_id, int $user_id): ?Message
    {
        return Message::where('id', $message_id)
                    ->where('reciver', $user_id)
                    ->first();
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

    /**
     * Retrieve the last 20 users ids conversations 
     *   involving a specific user.
     *
     * @param integer $user_id
     * @return array
     */
    public function last_friend_on_messages_history(int $user_id) : array
    {
        $lastConversations = Message::select('sender', 'reciver', 'created_at')
                ->where('sender', $user_id)
                ->orWhere('reciver', $user_id)
                ->groupBy('sender', 'reciver', 'created_at')
                ->orderByDesc('created_at')
                ->paginate(20);

        return $lastConversations->map(function ($val) use ($user_id) {
            return $user_id === $val->sender ? $val->reciver : null;
        })->filter()->toArray();
    }
}
