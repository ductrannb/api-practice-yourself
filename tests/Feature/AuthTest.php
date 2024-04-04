<?php

namespace Tests\Feature;

use App\Jobs\SendMailForgetPassword;
use App\Models\Otp;
use App\Models\User;
use App\Utils\Messages;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\TestResponse;

class AuthTest extends BaseTest
{
    use WithFaker;

    public function login($email, $password): TestResponse
    {
        return $this->postJson('api/login', ['email'=> $email, 'password' => $password]);
    }

    public function test_login_success()
    {
        $user = User::factory()->create();
        $data = ['email' => $user->email, 'password' => UserFactory::DEFAULT_PASSWORD];
        $response = $this->postJson('api/login', $data);
        $response->assertOk()->assertJson([
            'access_token' => true,
            'token_type' => true,
            'expires_in' => true
        ]);
    }

    public function test_login_failed()
    {
        $user = User::factory()->create();
        $data = ['email' => $user->email, 'password' => 'something'];
        $response = $this->postJson('api/login', $data);
        $response->assertUnauthorized()->assertJson([
            'message' => Messages::PASSWORD_INVALID_MESSAGE
        ]);
    }

    public function test_register_success()
    {
        $password = $this->faker->sentence;
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => $password,
            'password_confirmation' => $password
        ];
        $response = $this->postJson('api/register', $data);
        $response->assertCreated();
    }

    public function test_request_forget_password_success()
    {
        Queue::fake();
        $user = User::factory()->create();
        $response = $this->postJson('api/forget-password/request', ['email' => $user->email]);
        $response->assertOk()->assertJson(['message' => Messages::OTP_SEND_MESSAGE]);
        Queue::assertPushed(SendMailForgetPassword::class);
    }

    public function test_request_forget_password_failed()
    {
        $response = $this->postJson('api/forget-password/request', ['email' => $this->faker->email]);
        $response->assertUnprocessable();
    }

    public function test_forget_password_success()
    {
        $user = User::factory()->create();
        $otp = Otp::factory()->create(['email' => $user->email]);
        $password = $this->faker->sentence;
        $data = [
            'email' => $user->email,
            'otp' => $otp->code,
            'new_password' => $password,
            'new_password_confirmation' => $password
        ];
        $response = $this->postJson('api/forget-password', $data);
        $response->assertOk();
        $this->login($user->email, $password)->assertOk();
    }

    public function test_change_password_success()
    {
        $password = $this->faker->sentence;
        $newPassword = $this->faker->sentence;
        $user = User::factory()->create(['password' => bcrypt($password)]);
        $token = $this->getToken($user);
        $data = ['password' => $password, 'new_password' => $newPassword, 'new_password_confirmation' => $newPassword];
        $response = $this->withToken($token)->postJson('api/change-password', $data);
        $response->assertOk();
        $this->login($user->email, $newPassword)->assertOk();
    }

    public function test_change_password_failed()
    {
        $password = $this->faker->sentence;
        $newPassword = $this->faker->sentence;
        $user = User::factory()->create(['password' => bcrypt($password)]);
        $data = ['password' => $password, 'new_password' => $newPassword, 'new_password_confirmation' => $newPassword];
        $response = $this->postJson('api/change-password', $data);
        $response->assertUnauthorized();
    }

    public function test_me_success()
    {
        $response = $this->withToken($this->getToken())->get('api/me');
        $response->assertOk();
    }

    public function test_me_failed()
    {
        $response = $this->get('api/me');
        $response->assertUnauthorized();
    }

    public function test_logout_success()
    {
        $token = $this->getToken();
        $response = $this->withToken($token)->get('api/logout');
        $response->assertOk();
        $this->withToken($token)->get('api/me')->assertUnauthorized();
    }

    public function test_refresh_success()
    {
        $token = $this->getToken();
        $response = $this->withToken($token)->post('api/refresh');
        $response->assertOk();
        $newToken = $response->json('access_token');
        $this->withToken($token)->get('api/me')->assertUnauthorized();
        $this->withToken($newToken)->get('api/me')->assertOk();
    }
}
