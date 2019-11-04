<?php

namespace Tests\Feature\Users;

use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_user_cannot_see_fetch_user_information()
    {
        $this->withExceptionHandling();

        $this->json('GET', route('user.show'))
            ->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_can_fetch_their_own_information()
    {
        Passport::actingAs(
            $user = factory(User::class)->create()
        );

        $this->json('GET', route('user.show'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'user'
            ])
            ->assertJsonFragment([
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                ]
            ]);
    }

}
