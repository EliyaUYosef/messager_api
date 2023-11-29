<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct(){}
    public function create_user($user_object) {
        return User::create($user_object);
    }
    public function update_last_action_time() {
        $token = '';
        if (Auth::check()) {
            $user = Auth::user();
            $update_result = User::where('id', $user->id)->update(['last_action_time' => now()]);
            $token = $user->createToken("myToken")->accessToken;
        }
        return $token;
    }
    public function get_user_by_id($user_id) {
        return User::find($user_id);
    }
}
