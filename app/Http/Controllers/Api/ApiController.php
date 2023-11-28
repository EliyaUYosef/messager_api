<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class ApiController extends Controller
{
   // Register Api (POST)
   public function register(Request $request) {
        
       // Data validation
       // fix validation - more specific
       $request->validate(User::$validations_roles);


       // Author model
       User::create([
           "name" => $request->name,
           "email" => $request->email,
           "password" => Hash::make($request->password)
       ]);
       // add return error on failure
       // Response
       return response()->json([
           "status" => true,
           "message" => "User created successfully"
       ]);
   }


   // Login Api (POST)
   public function login(Request $request) {
       // Data validation
       $request->validate([
           "email" => "required|email",
           "password" => "required"
       ]);
      
       // Auth Facade
       if(Auth::attempt([
           "email" => $request->email,
           "password" => $request->password
       ])){
           $user = Auth::user();
          
           $token = $user->createToken("myToken")->accessToken;
           // add to db indicate if user is on online
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
   public function profile() {
       $userdata = Auth::user();


       return response()->json([
           "status" => true,
           "message" => "Profile data",
           "data" => $userdata
       ]);
   }


   // Logout Api (GET)
   public function logout() {
       auth()->user()->token()->revoke();


       return response()->json([
           "status" => true,
           "message" => "User logged out"
       ]);
   }


   // Write Message API (POST)
   public function write_message(Request $request) {
       $userdata = Auth::user();
       // check if reciver exist
       // check if subject or message is empty
       $request->validate(Message::$vlidations_roles);
       Message::create([
           'sender' => $userdata['id'],
           'reciver' => $request->reciver,
           'subject' => $request->subject,
           'message' => $request->message,
       ]);
       // if not created, return error on failure
       return response()->json([
           "status" => true,
           "message" => "Message created"
       ]);
   }


   // Get All Unread Messages - API (GET)
   public function get_all_unread_messages() {
       $userdata = Auth::user();
       $messages = Message::where('reciver',$userdata['id'])->where('recived_time',NULL)->orderBy('id','DESC')->get();
       // if failed, return error
       return response()->json([
           "status" => true,
           "data"=>$messages,
           "message" => "This the all messages"
       ]);
   }


   // Read Message - By 'id<Integer>' API (POST)
   public function read_message(Request $request) {
       $userdata = Auth::user();
       $request->validate([
           'message_id' => "required"
       ]);
       // check if message exist
       // check if message link to this user


       $update_status = Message::where('id',$request->message_id)->update(['recived_time'=>now()]);
       $message = Message::find($request->message_id);
      
       if ($update_status ) {
           return response()->json([
               "status" => true,
               "data"=>$message ?? [],
               "message" => "This message is already showed to user"
           ]);   
       }


       return response()->json([
           "status" => false,
           "message" => "Update is failed"
       ]);
   }


   // Delete Message - By 'id<Integer>' API (POST)
   public function delete_message(Request $request) {
       $userdata = Auth::user();
       $request->validate([
           'message_id' => "required"
       ]);
       // check if message is exist and link to this user
       $update_status = Message::where('id',$request->message_id)->delete();
      
       if ($update_status ) {
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
