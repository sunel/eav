<?php

namespace Eav\TestCase\Feature;

use Eav\Attribute;
use Tests\TestCase as Testbench;
use Illuminate\Database\Eloquent\Factory as ModelFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Cars extends \Eav\Model
{
    const ENTITY  = 'car';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'purchased_at'
    ];
}

abstract class TestCase extends Testbench
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        if (! $this->app) {
            $this->refreshApplication();
        }

        $this->app->afterResolving('migrator', function ($migrator) {
            $migrator->path(realpath(__DIR__ . '/../migrations'));
        });

        parent::setUp();

        app()->make(ModelFactory::class)->load(__DIR__.'/../factories');
    }

    protected function addSku($override = null)
    {
        $data = [
            'attribute_code' => 'sku',
            'entity_code' => 'custom',
            'backend_class' => null,
            'backend_type' => 'string',
            'backend_table' =>  null,
            'frontend_class' =>  null,
            'frontend_type' => 'text',
            'frontend_label' => ucwords(str_replace('_', ' ', 'sku')),
            'source_class' =>  null,
            'default_value' => '',
            'is_required' => 0,
            'required_validate_class' =>  null
        ];

        if ($override) {
            $data = array_merge($data, $override);
        }

        return Attribute::add($data);
    }
}
