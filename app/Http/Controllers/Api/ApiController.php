<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\MessageService;
use App\Services\UserService;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App;

class ApiController extends Controller
{
    protected $userController;
    protected $messageController;
    public function __construct()
    {
        App::make(MessageService::class);
        App::make(UserService::class);
    }
    // Register Api (POST)
    public function register(Request $request)
    {
        // Data validation
        $request->validate(User::$register_rules); // Return HTTP status code 422 on error
        
        // Author model
        $this->userController->create_user([
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

    // Login Api (POST)
    public function login(Request $request)
    {
        // Data validation
        $request->validate(User::$login_rules);

        // Auth Facade
        if (Auth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ])) {
            
            
            $this->userController->update_last_action_time();
            
            $user = Auth::user();
            
            $token = $user->createToken("myToken")->accessToken;

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

    // Profile Api (GET)
    public function profile()
    {
        $userdata = Auth::user();

        return response()->json([
            "status" => true,
            "message" => "Profile data",
            "data" => $userdata
        ]);
    }

    // Logout Api (GET)
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
        $user = $this->userController
                    ->get_user_by_id($request->reciver);
        
        $request->validate(
            $this->messageController
                ->get_message_validations_rules()
        );
        
        if (!$user) {
            return response()->json([
                "status" => false,
                "message" => "Message reciver is not recognized."
            ]);
        }
        
        
        $message = $this->messageController->create_message([
            'sender' => $request['id'],
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
        $messages = $this->messageController
                        ->get_unread_messages_by_user_id($userdata['id']);
            
        
        return response()->json([
            "status" => true,
            "data" => $messages ?? [],
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
        
        $message_exist_result = $this->messageController
                            ->check_if_message_exist($request->id,'reciver', $userdata['id']);

        if (!$message_exist_result) {
            return response()->json([
                "status" => false,
                "message" => "Message not found"
            ]);
        }
        
        $update_status = $this->messageController
                            ->mark_as_read($request->message_id);

        $message = $this->messageController
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
        $request->validate([
            'message_id' => "required"
        ]);
        
        // check if message is exist and link to this user
        $update_status = $this->messageController
                            ->delete_message_by_id($request->message_id);

        if ($update_status) {
            return response()->json([
                "status" => true,
                "message" => "This message already deleted."
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "Delete is failed"
        ]);
    }
}
