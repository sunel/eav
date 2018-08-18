<?php

use Faker\Generator;
use Eav\Attribute;

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

$factory->define(Attribute::class, function (Generator $faker) {
    return [
        'attribute_code' => 'sku',
		'entity_code' => 'product',
		'backend_class' => NULL,
		'backend_type' => 'string',
		'backend_table' =>  NULL,
		'frontend_class' =>  NULL,
		'frontend_type' => 'text',
		'frontend_label' => ucwords(str_replace('_',' ','sku')),
		'source_class' =>  NULL,
		'default_value' => '',
		'is_required' => 0,
		'required_validate_class' =>  NULL
    ];
});

$factory->state(Attribute::class, 'select', function ($faker) {
    return [
    	'attribute_code' => 'search',
    	'backend_type' => 'boolean',
        'frontend_type' => 'select',
        'source_class' =>  \Eav\Attribute\Source\Boolean::class,
        'default_value' => 0,
    ];
});

$factory->state(Attribute::class, 'select_migration', function ($faker) {
    return [
    	'attribute_code' => 'search',
    	'backend_type' => 'boolean',
        'frontend_type' => 'select',
        'options' => [
	       '1' => 'Yes',
	       '0'  => 'No'
	     ],
        'default_value' => 0,
    ];
});