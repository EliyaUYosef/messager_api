<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::get('test',fn() => phpinfo());

// Open Routes
Route::post("auth/login", [ApiController::class,'login']);

Route::post("auth/register", [ApiController::class,'register']);


// Protected Group Routes
Route::group([
   "middleware" => ["auth:api", "update_user_last_action"]
   // "middleware" => ["auth:api"]
],function(){
   Route::get("auth/profile", [ApiController::class,'profile']);

   Route::get("auth/logout", [ApiController::class,'logout']);
  
   Route::post("msg/send_message", [ApiController::class,'send_message']);

   Route::post("msg/update_message_as_read", [ApiController::class,'update_message_as_read']);

   Route::post("msg/delete_message", [ApiController::class,'delete_message']);

         
// -----------------------------------

   Route::get("msg/get_chat_with", [ApiController::class,'get_chat_with']);

   Route::get("msg/get_unread_messages_from", [ApiController::class,'get_unread_messages_from']);
   Route::get("msg/get_last_convesations", [ApiController::class,'get_last_chats_peoples']);

});
      