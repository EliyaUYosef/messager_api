<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthProcessTests extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic test example.
     * @test
     */
    public function test_register_test(): void
    {
        $register_form_fields = [
            'name' => "Eliya Yosef",
            'email' => "Eliyas1@example.com",
            "password" => "A12345678",
            "password_confirmation" => "A12345678"
        ];
        
        $response = $this->post('/api/register',$register_form_fields);
        $response->assertStatus(200);

        $this->assertEquals('User created successfully', $response->getData()->message);
    }
    
    // public function test_login_test(): void
    // {
    //     $login_form_fields = [
    //         'email' => "tuna@example.com",
    //         "password" => "12345678",
    //     ];
        
    //     $response = $this->post('/api/login',$login_form_fields);
    //     $response->assertStatus(200);

    //     $this->assertEquals('Login successful.', $response->getData()->message);
    // }
}
