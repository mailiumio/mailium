<?php

namespace Tests\Feature\Lists;

use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use App\Models\SubscriberList;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_user_cannot_see_a_list()
    {
        $this->withExceptionHandling();

        $list = factory(SubscriberList::class)->create();

        $this->json('GET', route('lists.show', $list->id))
            ->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_cannot_see_another_users_list()
    {
        $this->withExceptionHandling();

        Passport::actingAs(
            factory(User::class)->create()
        );
        $list = factory(SubscriberList::class)->create();

        $this->json('GET', route('lists.show', $list->id))
            ->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_can_see_their_own_teams_lists()
    {
        $user = Passport::actingAs(
            factory(User::class)->create()
        );
        $team = factory(Team::class)->create([
            'user_id' => $user->id,
        ]);
        $list = factory(SubscriberList::class)->create([
            'team_id' => $team->id,
        ]);

        $this->json('GET', route('lists.show', $list->id))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                ]
            ])
            ->assertJsonFragment([
                'id' => $list->id,
                'name' => $list->name,
            ]);
    }

    /** @test */
    public function an_authenticated_user_can_see_their_a_list_of_an_associated_team()
    {
        $user = Passport::actingAs(
            factory(User::class)->create()
        );
        $team = factory(Team::class)->create();

        $user->linkedTeams()->attach($team);
        
        $list = factory(SubscriberList::class)->create([
            'team_id' => $team->id,
        ]);

        $this->json('GET', route('lists.show', $list->id))
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $list->id,
                'name' => $list->name,
            ]);
    }

}
