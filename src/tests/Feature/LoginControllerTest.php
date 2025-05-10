<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;


class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_fails_without_email()
    {
        User::factory()->make([
            'email' => 'test@example.com'
        ]);

        $this->post('/login', [
            'password' => 'password'
        ]);

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('メールアドレスを入力してください', $errors['email'][0]);

    }

    public function test_login_fails_without_password()
    {
        User::factory()->make([
            'email' => 'aaa@example.com'
        ]);

        $this->post('/login', [
            'email' => 'aaa@example.com'
        ]);

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('パスワードを入力してください', $errors['password'][0]);

    }

    public function test_login_non_exit_user()
    {
        User::factory()->make([
            'email' => 'aaa@example.com'
        ]);

        $this->post('/login', [
            'email' => 'bbb@example.com',
            'password' => '12345678'
        ]);

        $errors = session('fail')->getMessages();

        // 後で直す
        $this->assertEquals('ログイン情報が登録されていません', $errors['fail'][0]);

    }

    public function test_login_success()
    {
        User::factory()->make([
            'email' => 'aaa@example.com'
        ]);

        $this->post('/login', [
            'email' => 'aaa@example.com',
            'password' => 'password'
        ]);

        $response = $this->get('/');
        $response->assertViewIs('top');
    }

    public function test_logout_user(){
        User::factory()->make([
            'email' => 'aaa@example.com'
        ]);

        $response = $this->post('/login', [
            'email' => 'aaa@example.com',
            'password' => 'password'
        ]);

        $response = $this->post('/logout');
        $response->assertViewIs('auth.login');
    }
}
