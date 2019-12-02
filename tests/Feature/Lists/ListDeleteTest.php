<?php

namespace Tests\Feature\Lists;

use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use App\Models\SubscriberList;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_user_cannot_delete_a_list()
    {
        $this->withExceptionHandling();

        $list = factory(SubscriberList::class)->create();

        $this->json('DELETE', route('lists.destroy', $list->id))
            ->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_cannot_delete_other_users_list()
    {
        $this->withExceptionHandling();

        Passport::actingAs(
            factory(User::class)->create()
        );
        $list = factory(SubscriberList::class)->create();

        $this->json('DELETE', route('lists.destroy', $list->id))
            ->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_can_delete_their_own_list()
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

        $this->json('DELETE', route('lists.destroy', $list->id))
            ->assertStatus(200);

        $this->assertDatabaseMissing('lists', [
            'id' => $list->id,
        ]);
    }

    /** @test */
    public function an_authenticated_user_can_delete_an_associated_list()
    {
        $user = Passport::actingAs(
            factory(User::class)->create()
        );
        $team = factory(Team::class)->create();
        $list = factory(SubscriberList::class)->create([
            'team_id' => $team->id,
        ]);

        $user->linkedTeams()->attach($team);

        $this->json('DELETE', route('lists.destroy', $list->id))
            ->assertStatus(200);

        $this->assertDatabaseMissing('lists', [
            'id' => $list->id,
        ]);
    }

}
