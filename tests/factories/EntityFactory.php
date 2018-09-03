<?php

use Faker\Generator;
use Eav\Entity;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(Entity::class, function (Generator $faker) {
    $code = $faker->unique()->userName;
    return [
        'entity_code' => $code,
        'entity_class' => 'App\\'.ucfirst($code),
        'entity_table' => $code.'s',
    ];
});


$factory->state(Entity::class, 'flat', function ($faker) {
    return [
        'is_flat_enabled' => 1
    ];
});
