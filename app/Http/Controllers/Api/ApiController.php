<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\MessageService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

/**
 * Main Api Controller - Http Terminal Requests
 */
class ApiController extends Controller
{
    /**     
     * User Action Center Service Link
     *
     * @var UserService $userService 
     */
    protected UserService $userService;

    /**
     * Message Action Center Service Link
     *
     * @var MessageService $messageService
     */
    protected MessageService $messageService;

    /**
     * Class Construct -  Make Services Run
     */
    public function __construct()
    {
        // create as singletone - initial only once for the entire app
        $this->userService = app()->make(UserService::class);
        $this->messageService = app()->make(MessageService::class);
    }


    /**
     * Register Api
     * Method : POST
     * Path : api/auth/register
     * 
     * @auth User
     * @param Request $request (name, email, password, password_confirmation)
     * @return Response (HTTP-JSON)
     */
    public function register(Request $request) : \Illuminate\Http\JsonResponse
    {
        // Data validation
        $request->validate(
            $this->userService->register_validations_rule()
        ); // Return HTTP status code 422 on failure

        // Author model
        $new_user = $this->userService->create_user([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);

        // Check user creation result
        if (!$new_user) {
            return response()->json([
                "message" => "User creation is failed."
            ])->setStatusCode(500);
        }

        // Response
        return response()->json([
            "message" => "User created successfully.",
            "data" => ["user" => $new_user]
        ])->setStatusCode(201);
    }

    /**
     * Login Api 
     * Method : POST
     * Path : api/auth/login
     *
     * @auth User
     * @param Request $request (email, password)
     * @return Response (HTTP-JSON)
     */
    public function login(Request $request) : \Illuminate\Http\JsonResponse
    {
        // Data validation
        $request->validate(
            $this->userService->login_validations_rules()
        ); // Return HTTP status code 422 on error

        // Auth Facade
        if (Auth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ])) {

            $token = $this->userService->generate_token();
            $this->userService->update_last_action_time();
            $new_user = Auth::user();

            return response()->json([
                "message" => "Login successful.",
                "data" => [
                    "access_token" => $token,
                    "user" => $new_user
                ]
            ])->setStatusCode(200);
        }

        return response()->json([
            "message" => "Invalid credentials."
        ])->setStatusCode(401);
    }

    /**
     * Profile Api
     * Method : GET ()
     * Path : api/auth/profile
     * 
     * Desc : get all user info data from db
     * 
     * 
     * @auth User
     * @param []
     * @return Response (HTTP-JSON) 
     */
    public function profile() : \Illuminate\Http\JsonResponse
    {
        // Authentication check
        if (!Auth::check()) {
            return response()->json([
                "message" => "You must be logged in to get user info."
            ])->setStatusCode(401);
        }

        // Get user data
        $user_data = Auth::user();

        // Response
        return response()->json([
            "message" => "User profile data.",
            "data" => $user_data
        ])->setStatusCode(200);
    }

    /**
     * Logout Api
     * Method : GET ()
     * Path : a[i/auth/logout
     * 
     * 
     * @auth User
     * @param []
     * @return Response (HTTP-JSON)
     */
    public function logout() : \Illuminate\Http\JsonResponse
    {
        // Authentication check
        if (!Auth::check()) {
            return response()->json([
                "message" => "You must be logged in to get user info."
            ])->setStatusCode(401);
        }

        Auth::user()->token()->revoke();

        return response()->json([
            "message" => "User logged out."
        ])->setStatusCode(200);
    }

    /**
     * Insert New Message
     * Method : POST
     * Path : api/msg/insert_new_message
     * 
     * Desc : add new message to db.
     *        the connectng user is the writer
     *
     * @auth User
     * @param Request $request (reciver, message, subject)
     * @return Response (HTTP_JSON)
     */
    public function insert_new_message(Request $request) : \Illuminate\Http\JsonResponse
    {
        // Authentication check
        if (!Auth::check()) {
            return response()->json([
                "message" => "You must be logged in to send a message."
            ])->setStatusCode(401);
        }
        $sender = Auth::user();

        // Data validation
        $request->validate(
            $this->messageService
                ->message_validations_rules()
        );

        $reciver_user = $this->userService
            ->get_user_by_id($request->reciver);

        if (!$reciver_user) {
            return response()->json([
                "message" => "Message reciver is not recognized."
            ])->setStatusCode(404);
        }

        $message_creation_result = $this->messageService->create_message([
            'sender' =>  $sender->id,
            'reciver' => $request->reciver,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        if ($message_creation_result) {
            return response()->json([
                "data" => [
                    "message" => $message_creation_result,
                    "reciver_info" => $reciver_user
                ],
                "message" => "Message created."
            ])->setStatusCode(201);
        }

        // Handle the case where message creation fails
        return response()->json([
            "message" => "Failed to create the message."
        ])->setStatusCode(500);
    }

    /**
     * Get Chat With
     * Metthod : GET
     * Path : api/msg/get_chat_with
     * 
     * Desc : get all messages from specific user
     *
     * @auth User
     * @param Request $request ( reciver - user_id )
     * @return Response (HTTP_JSON)
     */
    public function get_chat_with(Request $request) : \Illuminate\Http\JsonResponse
    {
        // Authentication check
        if (!Auth::check()) {
            return response()->json([
                "message" => "You must be logged in to get messages."
            ])->setStatusCode(401);
        }
        $user = Auth::user();

        // Data validation
        $request->validate($this->userService->user_id_validations()); // return 422 on error

        // Fetch the user with whom the chat is happening
        $chat_with_user = $this->userService->get_user_by_id($request->user_id);
        if (!$chat_with_user) {
            return response()->json([
                "message" => "Chat partner not found"
            ])->setStatusCode(404);
        }

        $chat_messages = $this->messageService
            ->get_messages_from_specific_user($user['id'], $request->user_id) ?? [];

        $count_messages = count($chat_messages);

        if ($count_messages > 0) {
            return response()->json([
                "data" => [
                    "messages" => $chat_messages,
                    "messages_count" => $count_messages,
                    "chat_with" => $chat_with_user
                ],
                "message" => "This the all messages."
            ])->setStatusCode(200);
        }

        return response()->json([
            "data" => [
                "messages" => $chat_messages,
                "messages_count" => $count_messages,
            ],
            "message" => "No messages found with the specific user."
        ])->setStatusCode(204);
    }

    /**
     * Get Unread Messages From
     * Metthod : GET
     * Path : api/msg/get_unread_messages_from
     * 
     * Desc : get unread messages from specific user
     * 
     * @auth User
     * @param Request $request ( reciver - user_id )
     * @return Response (HTTP_JSON)
     */
    public function get_unread_messages_from(Request $request) : \Illuminate\Http\JsonResponse
    {
        // Authentication check
        if (!Auth::check()) {
            return response()->json([
                "message" => "You must be logged in to get messages."
            ])->setStatusCode(401);
        }
        $user = Auth::user();

        // Data validation
        $request->validate($this->userService->user_id_validations()); // Return status 422 on error

        // Fetch the user with whom the chat is happening
        $chat_with_user = $this->userService->get_user_by_id($request->user_id);
        if (!$chat_with_user) {
            return response()->json([
                "message" => "Chat partner not found"
            ])->setStatusCode(404);
        }

        // Search for messages
        $messages_result = $this->messageService
            ->get_unread_messages_from_specific_user($user['id'], $request->user_id) ?? [];

        $count_messages = count($messages_result);

        if ($count_messages > 0) {
            return response()->json([
                "data" => [
                    "messages" => $messages_result,
                    "messages_count" => $count_messages,
                    "chat_with"=>$chat_with_user,
                ],
                "message" => "This the all messages."
            ])->setStatusCode(200);
        }

        return response()->json([
            "data" => [
                "messages" => $messages_result,
                "messages_count" => $count_messages
            ],
            "message" => "No unread messages found with the specific user."
        ])->setStatusCode(204);
    }

    /**
     * Update Message As (Already) Read
     * Metthod : POST
     * Path : api/msg/update_message_as_read
     *
     * Desc : update Message read flag to true by id
     * 
     * @auth User
     * @param Request $request (message_id)
     * @return Response (HTTP_JSON)
     */
    public function update_message_as_read(Request $request) : \Illuminate\Http\JsonResponse
    {
        // Authentication check
        if (!Auth::check()) {
            return response()->json([
                "message" => "You must be logged in to update message status."
            ])->setStatusCode(401);
        }
        $user = Auth::user();

        // Data validation
        $request->validate(
            $this->messageService->message_id_validations()
        );

        $message_exist_result = $this->messageService
            ->check_if_message_exist($request->message_id);

        if (!$message_exist_result) {
            return response()->json([
                "message" => "Message not found."
            ])->setStatusCode(404);
        }

        // Check if the receiver exists
        $receiver_exist_result = $this->messageService
            ->check_if_receiver_exist($request->message_id, $user->id);

        if (!$receiver_exist_result) {
            return response()->json([
                "message" => "Receiver not found."
            ])->setStatusCode(404);
        }

        $update_status = $this->messageService
            ->mark_as_read($request->message_id);

        $message = $this->messageService
            ->get_message_by_id($request->message_id);

        if ($update_status && $message) {
            return response()->json([
                "data" => [
                    "message" => $message
                ],
                "message" => "This message has been marked as read."
            ])->setStatusCode(200);
        }

        return response()->json([
            "message" => "Failed to mark the message as read."
        ])->setStatusCode(500);
    }

    /**
     * Delete Message
     * Metthod : POST
     * Path : api/msg/delete_message
     * 
     * Desc : remove message from db by id
     *
     * @auth User
     * @param Request $request (message_id)
     * @return Response (HTTP_JSON)
     */
    public function delete_message(Request $request) : \Illuminate\Http\JsonResponse
    {
        // Authentication check
        if (!Auth::check()) {
            return response()->json([
                "message" => "You must be logged in to delete a message."
            ])->setStatusCode(401);
        }
        $user = Auth::user();

        // Data validation
        $request->validate($this->messageService
            ->message_id_validations());

        $message_to_delete = $this->messageService
            ->get_message_by_id($request->message_id);

        if (
            !$message_to_delete ||
            ($message_to_delete->sender !== $user->id &&
                $message_to_delete->receiver !== $user->id)
        ) {
            return response()->json([
                "message" => "Message not found or you don't have permission to delete it.",
            ])->setStatusCode(404);
        }

        $delete_status = $this->messageService
            ->delete_message_by_id($request->message_id);

        if ($delete_status) {
            return response()->json([
                "message" => "The message has been successfully deleted."
            ])->setStatusCode(200);
        }

        $message_exist = $this->messageService->check_if_message_exist($request->message_id);
        if (!$message_exist) {
            return response()->json([
                "message" => "Message not found.",
            ])->setStatusCode(404);
        } else {
            return response()->json([
                "message" => "Failed to delete the message.",
            ])->setStatusCode(500);
        }
    }
}
