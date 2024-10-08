<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $user = User::where('email', 'yusuf@mail.com')->first();

        $token = JWTAuth::fromUser($user);

        $this->assertNotEmpty($token, 'JWT Token generation failed.');
        echo "Generated JWT Token: " . $token . "\n";

        $response = $this->withHeader('Authorization', "Bearer {$token}")->get('/api/user');
        
        $response->assertStatus(200);
    }
}
