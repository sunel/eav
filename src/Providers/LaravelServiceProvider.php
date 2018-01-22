<?php 

namespace Eav\Providers;

use Validator;
use Eav\Console\ModelMakeCommand;
use Eav\Console\EntityComplierCommand;
use Illuminate\Support\ServiceProvider;
use Eav\Migrations\EntityMigrationCreator;
use Eav\Migrations\AttributeMigrationCreator;
use Eav\Migrations\EntityAttributeMapCreator;
use Eav\Console\Migrations\EntityMigrateMakeCommand;
use Eav\Console\Migrations\EntityAttributeMapCommand;
use Eav\Console\Migrations\AttributeMigrateMakeCommand;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCreator();
         
        $this->registerCommands();
        
        $this->mergeConfigFrom(__DIR__.'/../../config/eav.php', 'eav');
    }


    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/eav.php' => config_path('eav.php'),
        ], 'config');
        
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/');
    }
    
    /**
    * Register the migration creator.
    *
    * @return void
    */
    protected function registerCreator()
    {
        $this->app->singleton('eav.entity.migration.creator', function ($app) {
            return new EntityMigrationCreator($app['files']);
        });
        
        $this->app->singleton('eav.attribute.migration.creator', function ($app) {
            return new AttributeMigrationCreator($app['files']);
        });
        
        $this->app->singleton('eav.entity.attribute.map.creator', function ($app) {
            return new EntityAttributeMapCreator($app['files']);
        });
    }
    
    /**
    * Register all of the migration commands.
    *
    * @return void
    */
    protected function registerCommands()
    {
        $commands = [
            'MakeEntityMigration', 'MakeEnityModel', 'MakeAttributeMigration',
            'MakeEntityAttributeMap', 'EntityComplier'
        ];

        // We'll simply spin through the list of commands that are migration related
        // and register each one of them with an application container. They will
        // be resolved in the Artisan start file and registered on the console.
        foreach ($commands as $command) {
            $this->{'register'.$command.'Command'}();
        }

        // Once the commands are registered in the application IoC container we will
        // register them with the Artisan start event so that these are available
        // when the Artisan application actually starts up and is getting used.
        $this->commands(
            'command.entity.migrate.make',
            'command.attribute.migrate.make',
            'command.entity.model.make',
            'command.entity.attribute.map.make',
            'command.entity.complier'
        );
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerEntityComplierCommand()
    {
        $this->app->singleton('command.entity.complier', function ($app) {
            return new EntityComplierCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMakeEnityModelCommand()
    {
        $this->app->singleton('command.entity.model.make', function ($app) {
            return new ModelMakeCommand($app['files']);
        });
    }
    
    /**
     * Register the "make enitity" migration command.
     *
     * @return void
     */
    protected function registerMakeEntityMigrationCommand()
    {
        $this->app->singleton('command.attribute.migrate.make', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['eav.entity.migration.creator'];

            $composer = $app['composer'];

            return new EntityMigrateMakeCommand($creator, $composer);
        });
    }
    
    /**
     * Register the "make attibute" migration command.
     *
     * @return void
     */
    protected function registerMakeAttributeMigrationCommand()
    {
        $this->app->singleton('command.entity.migrate.make', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['eav.attribute.migration.creator'];

            $composer = $app['composer'];

            return new AttributeMigrateMakeCommand($creator, $composer);
        });
    }
    
    /**
     * Register the "make entity attribute map" migration command.
     *
     * @return void
     */
    protected function registerMakeEntityAttributeMapCommand()
    {
        $this->app->singleton('command.entity.attribute.map.make', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['eav.entity.attribute.map.creator'];

            $composer = $app['composer'];

            return new EntityAttributeMapCommand($creator, $composer);
        });
    }
    
    
    /**
    * Get the services provided by the provider.
    *
    * @return array
    */
    public function provides()
    {
        return [
            'eav.entity.migration.creator',
            'eav.attribute.migration.creator',
            'eav.attribute.migration.creator',
            'command.entity.migrate.make',
            'command.attribute.migrate.make',
            'command.entity.model.make',
            'command.entity.attribute.map.make',
        ];
    }
}
