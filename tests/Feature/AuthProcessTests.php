<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthProcessTests extends TestCase
{
    /**
     * A basic test example.
     */
    public function register_test(): void
    {
        $register_form_fields = [
            'name' => "Eliya Yosef",
            'email' => "Eliya@example.com",
            "password" => "123456",
            "password_confirmation" => "123456"
        ];
        
        $response = $this->post('/api/register',$register_form_fields);
        dd($response);
        $response->assertStatus(200);
    }
}
