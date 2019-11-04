<?php

namespace Tests\Feature\Users;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Passport\Token;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserLogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_user_cannot_logout()
    {
        $this->withExceptionHandling();

        $this->json('POST', route('user.logout'))
            ->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_can_logout()
    {
        $user = factory(User::class)->create();
        $token = Token::create([
            'id' => Str::random(60),
            'user_id' => $user->id,
            'client_id' => 1,
            'scopes' => [],
            'revoked' => 0,
        ]);

        // Login user
        $user->withAccessToken($token);
        app('auth')->guard('api')->setUser($user);
        app('auth')->shouldUse('api');

        $this->json('POST', route('user.logout'))
            ->assertStatus(200);

        $this->assertDatabaseHas('oauth_access_tokens', [
            'id' => $token->id,
            'user_id' => $user->id,
            'revoked' => 1
        ]);
    }

}
