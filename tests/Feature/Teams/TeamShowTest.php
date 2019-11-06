<?php

namespace Tests\Feature\Teams;

use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_user_cannot_see_teams()
    {
        $this->withExceptionHandling();

        $team = factory(Team::class)->create();

        $this->json('GET', route('teams.show', $team->id))
            ->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_cannot_see_another_users_team()
    {
        $this->withExceptionHandling();

        Passport::actingAs(
            factory(User::class)->create()
        );
        $team = factory(Team::class)->create();

        $this->json('GET', route('teams.show', $team->id))
            ->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_can_see_their_own_team()
    {
        $user = Passport::actingAs(
            factory(User::class)->create()
        );
        $team = factory(Team::class)->create([
            'user_id' => $user->id,
        ]);

        $this->json('GET', route('teams.show', $team->id))
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id'
            ])
            ->assertJsonFragment([
                'id' => $team->id,
                'name' => $team->name,
                'user_id' => $user->id,
            ]);
    }

    /** @test */
    public function an_authenticated_user_can_see_another_users_team_that_they_belong_to()
    {
        $user = Passport::actingAs(
            factory(User::class)->create()
        );
        $team = factory(Team::class)->create();

        $user->linkedTeams()->attach($team);

        $this->json('GET', route('teams.show', $team->id))
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $team->id,
                'name' => $team->name,
                'user_id' => $team->owner->id,
            ]);
    }

}
