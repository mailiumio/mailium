<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Team;
use Faker\Generator as Faker;
use App\Models\SubscriberList;

$factory->define(SubscriberList::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'team_id' => factory(Team::class)->create()->id,
    ];
});
