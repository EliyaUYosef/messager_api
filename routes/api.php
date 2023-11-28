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
Route::post("register", [ApiController::class,'register']);
Route::post("login", [ApiController::class,'login']);


// Protected Routes
Route::group([
   "middleware" => ["auth:api"]
],function(){
   // GET Method
   Route::get("profile", [ApiController::class,'profile']);
   Route::get("logout", [ApiController::class,'logout']);
  
   Route::get("get_all_unread_messages", [ApiController::class,'get_all_unread_messages']);
  
   // POST Method
   Route::post("write_message", [ApiController::class,'write_message']);
   Route::post("read_message", [ApiController::class,'read_message']);
   Route::post("delete_message", [ApiController::class,'delete_message']);
});
