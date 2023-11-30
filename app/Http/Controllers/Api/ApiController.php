<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\MessageService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\User;
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
     *
     * @auth User
     * @param Request $request [
     *                          string name,
     *                          string email,
     *                          string password,
     *                          string password_confirmation
     * ]
     * @return Response (HTTP-JSON)
     */
    public function register(Request $request)
    {
        // Data validation
        $request->validate(User::$register_form_rules); // Return HTTP status code 422 on error

        // Author model
        $this->userService->create_user([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);

        // Response
        return response()->json([
            "status" => true,
            "message" => "User created successfully"
        ]);
    }

    /**
     * Login Api 
     * Method : POST
     *
     * @auth User
     * @param Request $request [
     *                          string email,
     *                          string password,
     * ]
     * @return Response (HTTP-JSON)
     */
    public function login(Request $request)
    {
        // Data validation
        $request->validate($this->userService->get_login_validations_rule());

        // Auth Facade
        if (Auth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ])) {

            $token = $this->userService->update_last_action_time();

            return response()->json([
                "status" => true,
                "message" => "Login successful",
                "access_token" => $token
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "Invalid credentials"
        ]);
    }

    /**
     * Profile Api
     * 
     * get all user fields datafrom db
     * 
     * Method : GET
     * 
     * @auth User
     * @param []
     * @return Response (HTTP-JSON) 
     */
    public function profile()
    {
        $userdata = Auth::user();

        return response()->json([
            "status" => true,
            "message" => "User profile data",
            "data" => $userdata
        ]);
    }

    /**
     * Logout Api
     *
     * Method : GET
     * 
     * @auth User
     * @param []
     * @return Response (HTTP-JSON)
     */
    public function logout()
    {
        auth()->user()->token()->revoke();

        return response()->json([
            "status" => true,
            "message" => "User logged out"
        ]);
    }


    // Write Message API (POST)
    public function write_message(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                "status" => false,
                "message" => "You must be logged in to send a message."
            ]);
        }

        $sender = Auth::user();
        $reciver_user = $this->userService
            ->get_user_by_id($request->reciver);

        $request->validate(
            $this->messageService
                ->get_message_validations_rules()
        );


        if (!$reciver_user) {
            return response()->json([
                "status" => false,
                "message" => "Message reciver is not recognized."
            ]);
        }


        $message = $this->messageService->create_message([
            'sender' =>  $sender->id,
            'reciver' => $request->reciver,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        if (!$message) {
            // Handle the case where message creation fails
            return response()->json([
                "status" => false,
                "message" => "Failed to create the message"
            ]);
        }

        return response()->json([
            "status" => true,
            "message" => "Message created"
        ]);
    }


    // Get All Unread Messages - API (GET)
    public function get_all_unread_messages()
    {
        $userdata = Auth::user();
        $messages = $this->messageService
            ->get_unread_messages_by_user_id($userdata['id']) ?? [];

        return response()->json([
            "status" => true,
            "data_length" => count($messages),
            "data" => $messages,
            "message" => "This the all messages"
        ]);
    }
    
    // Get All Messages From All - API (GET)
    public function get_messages_from_all()
    {
        Auth::check();
        $user = Auth::user();
        $messages = $this->messageService
            ->get_messages_for_user($user['id']) ?? [];

        return response()->json([
            "status" => true,
            "data_length" => count($messages),
            "data" => $messages,
            "message" => "This the all messages"
        ]);
    }


    // Read Message - By 'id<Integer>' API (POST)
    public function read_message(Request $request)
    {
        $userdata = Auth::user();
        $request->validate([
            'message_id' => "required|integer"
        ]);

        $message_exist_result = $this->messageService
            ->check_if_message_exist($request->id);

        if (!$message_exist_result) {
            return response()->json([
                "status" => false,
                "message" => "Message not found"
            ]);
        }

        $update_status = $this->messageService
            ->mark_as_read($request->message_id);

        $message = $this->messageService
            ->get_message_by_id($request->message_id);

        if ($update_status && $message) {
            return response()->json([
                "status" => true,
                "data" => $message,
                "message" => "This message is already showed to user"
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "Update is failed"
        ]);
    }


    // Delete Message - By 'id<Integer>' API (POST)
    public function delete_message(Request $request)
    {
        $request->validate($this->messageService
                ->get_delete_message_req_validations());

        // check if message is exist and link to this user
        $update_status = $this->messageService
            ->delete_message_by_id($request->message_id);

        if ($update_status) {
            return response()->json([
                "status" => true,
                "message" => "This message already deleted."
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "Delete is failed."
        ]);
    }
}
