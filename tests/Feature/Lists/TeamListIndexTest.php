<?php

namespace Tests\Feature\Lists;

use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use App\Models\SubscriberList;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamListIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_user_cannot_see_lists_for_a_team()
    {
        $this->withExceptionHandling();

        $team = factory(Team::class)->create();

        $this->json('GET', route('teams.lists.index', $team->id))
            ->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_cannot_fetch_lists_from_a_team_they_are_not_associated_with()
    {
        $this->withExceptionHandling();

        Passport::actingAs(
            factory(User::class)->create()
        );
        $team = factory(Team::class)->create();

        $this->json('GET', route('teams.lists.index', $team->id))
            ->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_can_see_lists_for_their_team()
    {
        $user = Passport::actingAs(
            factory(User::class)->create()
        );

        $team = factory(Team::class)->create([
            'owner_id' => $user->id,
        ]);

        $list = factory(SubscriberList::class)->create([
            'team_id' => $team->id,
        ]);

        $response = $this->json('GET', route('teams.lists.index', $team->id))
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ])
            ->assertJsonFragment([
                'id' => $list->id,
                'name' => $list->name,
            ]);
    }

}
