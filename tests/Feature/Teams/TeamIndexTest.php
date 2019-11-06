<?php

namespace Tests\Feature\Teams;

use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_user_cannot_list_teams()
    {
        $this->withExceptionHandling();

        $this->json('GET', route('teams.index'))
            ->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_can_list_their_own_teams()
    {
        $user = Passport::actingAs(
            factory(User::class)->create()
        );
        $team = factory(Team::class)->create([
            'user_id' => $user->id,
        ]);

        $this->json('GET', route('teams.index'))
            ->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonFragment([
                'id' => $team->id,
                'name' => $team->name,
                'user_id' => $team->user_id,
            ]);
    }
    /** @test */
    public function an_authenticated_user_can_list_their_linked_teams()
    {
        $user = Passport::actingAs(
            factory(User::class)->create()
        );
        $team = factory(Team::class)->create();

        $user->linkedTeams()->attach($team);

        $this->json('GET', route('teams.index'))
            ->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonFragment([
                'id' => $team->id,
                'name' => $team->name,
                'user_id' => $team->user_id,
            ]);
    }

    /** @test */
    public function an_authenticted_user_cannot_list_other_users_teams()
    {
        $this->withExceptionHandling();

        $user = Passport::actingAs(
            factory(User::class)->create()
        );
        $ownedTeam = factory(Team::class)->create([
            'user_id' => $user->id,
        ]);
        $otherTeam = factory(Team::class)->create();

        $this->json('GET', route('teams.index'))
            ->assertJsonCount(1);
    }

}
