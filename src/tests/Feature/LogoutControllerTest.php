<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    /* No.3 */
    public function test_logout_success()
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $user->password
        ]);
        $response = $this->get('/');
        $response->assertViewIs('top');

        $response = $this->post('/logout');
        $response->assertRedirect('/login');
    }
}
