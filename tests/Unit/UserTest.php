<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function is_associated_with_a_their_own_team()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->isAssociatedWith($team));
    }

    /** @test */
    public function is_associated_with_a_team_they_have_been_invited_to()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();

        $user->linkedTeams()->attach($team);

        $this->assertTrue($user->isAssociatedWith($team));
    }

    /** @test */
    public function is_not_associated_with_a_team_they_have_not_been_invited_to()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();

        $this->assertFalse($user->isAssociatedWith($team));
    }

}
