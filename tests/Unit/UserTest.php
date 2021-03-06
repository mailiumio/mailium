<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use App\Models\SubscriberList;
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
            'owner_id' => $user->id,
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

    /** @test */
    public function can_get_owned_teams_and_associated_teams()
    {
        $user = factory(User::class)->create();
        $ownedTeam = factory(Team::class)->create([
            'owner_id' => $user->id,
        ]);
        $linkedTeam = factory(Team::class)->create();
        $user->linkedTeams()->attach($linkedTeam);

        $this->assertCount(2, $user->teams());
    }

    /** @test */
    public function can_get_owned_and_associated_lists()
    {
        $user = factory(User::class)->create();
        $ownedTeam = factory(Team::class)->create([
            'owner_id' => $user->id,
        ]);
        $linkedTeam = factory(Team::class)->create();
        $user->linkedTeams()->attach($linkedTeam);

        $ownedList = factory(SubscriberList::class)->create([
            'team_id' => $ownedTeam->id,
        ]);
        $linkedList = factory(SubscriberList::class)->create([
            'team_id' => $linkedTeam->id,
        ]);

        $this->assertCount(2, $user->lists());
    }

}
