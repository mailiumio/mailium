<?php

namespace Tests\Feature\Lists;

use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use App\Models\SubscriberList;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListUpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function an_unauthenticated_user_cannot_update_a_list()
    {
        $this->withExceptionHandling();

        $list = factory(SubscriberList::class)->create();

        $this->json('PATCH', route('lists.update', $list->id))
            ->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_cannot_update_another_users_list()
    {
        $this->withExceptionHandling();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $list = factory(SubscriberList::class)->create();

        $this->json('PATCH', route('lists.update', $list->id))
            ->assertStatus(403);
    }

    /** @test */
    public function the_name_must_be_a_string()
    {
        $this->withExceptionHandling();

        $user = Passport::actingAs(
            factory(User::class)->create()
        );
        $team = factory(Team::class)->create([
            'owner_id' => $user->id,
        ]);
        $list = factory(SubscriberList::class)->create([
            'team_id' => $team->id,
        ]);

        $this->json('PATCH', route('lists.update', $list->id), [
            'name' => [1, 2, 3]
        ])
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function the_name_can_be_updated()
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

        $this->json('PATCH', route('lists.update', $list->id), [
            'name' => $new_name = $this->faker->word,
        ])
            ->assertJsonStructure(['data'])
            ->assertJsonFragment([
                'id' => $list->id,
                'name' => $new_name,
            ]);

        $this->assertDatabaseHas('lists', [
            'id' => $list->id,
            'name' => $new_name,
        ]);
    }

}
