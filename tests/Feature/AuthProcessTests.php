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
            'email' => "Eliya1@example.com",
            "password" => "12345678",
            "password_confirmation" => "12345678"
        ];
        
        $response = $this->post('/api/register',$register_form_fields);
        $response->assertStatus(200);

        $this->assertEquals('User created successfully', $response->getData()->message);
    }
}
