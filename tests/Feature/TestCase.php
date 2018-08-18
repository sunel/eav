<?php

namespace Eav\TestCase\Feature;

use Orchestra\Testbench\TestCase as Testbench;
use Eav\Providers\LaravelServiceProvider;

abstract class TestCase extends Testbench
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/../factories');
        $this->artisan('migrate', ['--database' => 'testing']);
        $this->artisan('migrate', ['--path' => __DIR__ . '/database/migrations']);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [LaravelServiceProvider::class];
    }
}