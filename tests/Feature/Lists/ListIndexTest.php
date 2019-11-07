<?php

namespace Tests\Feature\Lists;

use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use App\Models\SubscriberList;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_user_cannot_see_lists()
    {
        $this->withExceptionHandling();

        $this->json('GET', route('lists.index'))
            ->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_can_see_their_owned_lists()
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

        $response = $this->json('GET', route('lists.index'))
            ->assertJsonStructure(['data'])
            ->assertJsonFragment([
                'id' => $list->id,
                'name' => $list->name,
            ]);
    }

    /** @test */
    public function an_authenticated_user_can_see_their_associated_lists()
    {
        $user = Passport::actingAs(
            factory(User::class)->create()
        );

        $team = factory(Team::class)->create();

        $user->linkedTeams()->attach($team);

        $list = factory(SubscriberList::class)->create([
            'team_id' => $team->id,
        ]);

        $response = $this->json('GET', route('lists.index'))
            ->assertJsonStructure(['data'])
            ->assertJsonFragment([
                'id' => $list->id,
                'name' => $list->name,
            ]);
    }

    /** @test */
    public function an_authenticted_user_cannot_list_other_users_lists()
    {
        $user = Passport::actingAs(
            factory(User::class)->create()
        );
        $team = factory(Team::class)->create([
            'owner_id' => $user->id,
        ]);
        $ownedList = factory(SubscriberList::class)->create([
            'team_id' => $team->id,
        ]);
        $otherList = factory(SubscriberList::class)->create();

        $this->json('GET', route('lists.index'))
            ->assertJsonCount(1);
    }

}
