<?php

namespace Tests\Feature\Lists;

use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListCreateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function an_unauthenticated_user_cannot_create_a_list()
    {
        $this->withExceptionHandling();

        $this->json('POST', route('lists.store', []))
            ->assertStatus(401);
    }

    /** @test */
    public function a_list_requires_a_name()
    {
        $this->withExceptionHandling();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('POST', route('lists.store', []))
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function the_name_must_be_a_string()
    {
        $this->withExceptionHandling();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('POST', route('lists.store', [
            'name' => [1, 2, 3],
        ]))->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function a_list_requires_a_team()
    {
        $this->withExceptionHandling();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('POST', route('lists.store', []))
            ->assertJsonValidationErrors(['team_id']);
    }

    /** @test */
    public function the_user_must_be_associated_with_the_team()
    {
        $this->withExceptionHandling();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('POST', route('lists.store', [
            'team_id' => 9999
        ]))->assertJsonValidationErrors(['team_id']);
    }

    /** @test */
    public function a_user_can_create_a_list_for_their_own_team()
    {
        $user = Passport::actingAs(
            factory(User::class)->create()
        );
        $team = factory(Team::class)->create([
            'owner_id' => $user->id,
        ]);

        $this->json('POST', route('lists.store', [
            'name' => $name = $this->faker->word,
            'team_id' => $team->id,
        ]))
            ->assertJsonStructure(['data'])
            ->assertJsonFragment([
                'name' => $name,
            ]);

        $this->assertDatabaseHas('lists', [
            'name' => $name,
            'team_id' => $team->id,
        ]);
    }

    /** @test */
    public function a_user_can_create_a_list_for_their_associated_team()
    {
        $user = Passport::actingAs(
            factory(User::class)->create()
        );
        $team = factory(Team::class)->create();

        $user->linkedTeams()->attach($team);

        $this->json('POST', route('lists.store', [
            'name' => $name = $this->faker->word,
            'team_id' => $team->id,
        ]))
            ->assertJsonStructure(['data'])
            ->assertJsonFragment([
                'name' => $name,
            ]);

        $this->assertDatabaseHas('lists', [
            'name' => $name,
            'team_id' => $team->id,
        ]);
    }

}
