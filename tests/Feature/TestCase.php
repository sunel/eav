<?php

namespace Eav\TestCase\Feature;

use Tests\TestCase as Testbench;
use Illuminate\Database\Eloquent\Factory as ModelFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends Testbench
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        app()->make(ModelFactory::class)->load(__DIR__.'/../factories');

        $this->artisan('migrate', ['--path' => __DIR__ . '/../migrations/']);
    }
}