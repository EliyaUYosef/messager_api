<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class UserService
{
    /**
     * Add user to DB
     *
     * @param array $user_object
     * @return User
     */
    public function create_user(array $user_object) : User
    {
        return User::create($user_object);
    }

    /**
     * Save the timestamp of any api call
     *
     * @return integer
     */
    public function update_last_action_time() : int
    {
        $user = Auth::user();
        return User::where('id', $user->id)->update(['last_action_time' => now()]);
    }
    
    /**
     * Generate authentication token
     *
     * @return string
     */
    public function generate_token() : string
    {
        $token = '';
        if (Auth::check()) {
            $user = Auth::user();
            
            $token = $user->createToken('app_token')->accessToken;
        }
        return $token;
    }

    /**
     * Find user by user_id
     *
     * @param integer $user_id
     * @return User|null
     */
    public function get_user_by_id(int $user_id) : ?User
    {
        $user = User::find($user_id);

        if ($user) {
            return $user;
        }
    
        // Handle the case where the user is not found
        return null;
    }

    /**
     * Return validations rules array for login form
     *
     * @return array
     */
    public function login_validations_rules() : array
    {
        return User::$login_form_rules;
    }
    
    /**
     * Return validations rules array for register form
     *
     * @return array
     */
    public function register_validations_rule() : array
    {
        return User::$register_form_rules;
    }
    
    /**
     * Return validations rules array for user id req
     *
     * @return array
     */
    public function user_id_validations() : array 
    {
        return User::$validations_id_rules;
    }

    /**
    * Get a collection of users based on their IDs.
    *
    * @param array $user_ids
    * @return Collection|null
    */
    public function get_users_list(array $user_ids) : ?Collection
    {
        return User::whereIn('id', $user_ids)->get();
    }
}
