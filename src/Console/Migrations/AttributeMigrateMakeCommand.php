<?php

namespace Eav\Console\Migrations;

use League\Csv\Exception as CsvException;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Eav\Migrations\AttributeMigrationCreator;

class AttributeMigrateMakeCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'eav:make:attribute {entity : The name of the entity.}
		{--A|attributes= : List of attributes.}
        {--S|source= : Location of the attributes to be created [CSV].}
        {--path= : The location where the migration file should be created.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file for attribute';

    /**
     * The migration creator instance.
     *
     * @var \Illuminate\Database\Migrations\MigrationCreator
     */
    protected $creator;

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a new migration install command instance.
     *
     * @param  \Eav\Migrations\AttributeMigrationCreator  $creator
     * @param  \Illuminate\Support\Composer  $composer
     * @return void
     */
    public function __construct(AttributeMigrationCreator $creator, Composer $composer)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $entity = $this->argument('entity');

        $attributes = $this->option('attributes');

        $source = $this->option('source');

        $path = $this->getMigrationPath();

        if (is_null($attributes) && is_null($source)) {
            $this->error('Either --attributes or --source must be given');
            exit(1);
        }

        try {
            if (!is_null($attributes)) {
                list($file, $attributes) = $this->creator->createFromString($attributes, $entity, $path);
            } else {
                list($file, $attributes) = $this->creator->createFromSource($source, $entity, $path);
            }
        } catch (CsvException | \Exception $e) {
            $this->error($e->getMessage());
            exit(1);
        }

        $file = pathinfo($file, PATHINFO_FILENAME);
        $this->info("Created Migration: $file");
        
        $this->call('eav:map:attribute', ['attributes' => implode(',', $attributes), 'entity' => $entity]);
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return $this->laravel->basePath().'/'.$targetPath;
        }

        return $this->laravel->databasePath().'/migrations';
    }
}
