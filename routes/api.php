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



Route::get('check',fn() => phpinfo());
// Open Routes
// - - - - - - - - - - - - - - - - - -

Route::post("auth/login", [ApiController::class,'login']);
Route::post("auth/register", [ApiController::class,'register']);

// Protected Routes
Route::group([
   "middleware" => ["auth:api", "update_user_last_action"]
],function(){
   // GET Method
   Route::get("auth/profile", [ApiController::class,'profile']);
   Route::get("auth/logout", [ApiController::class,'logout']);
  
   

   Route::post("msg/write_message", [ApiController::class,'write_message']);
   Route::get("msg/get_messages_from_all", [ApiController::class,'get_messages_from_all']);
   Route::get("msg/get_all_unread_messages", [ApiController::class,'get_all_unread_messages']);  
   Route::post("msg/read_message", [ApiController::class,'read_message']);
   Route::post("msg/delete_message", [ApiController::class,'delete_message']);
});
