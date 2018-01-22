<?php

namespace Eav\Console\Migrations;

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
    protected $signature = 'eav:make:attribute {attributes : List of attributes.}
		{entity : The name of the entity.}
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
        $attributes = $this->input->getArgument('attributes');
        
        $entity = $this->input->getArgument('entity');

        $this->writeMigration($attributes, $entity);
        
        $this->call('eav:map:attribute', ['attributes' => $attributes, 'entity' => $entity]);
    }

    /**
     * Write the migration file to disk.
     *
     * @param  string  $attributes
     * @param  string  $table
     * @param  bool    $create
     * @return string
     */
    protected function writeMigration($attributes, $entity)
    {
        $path = $this->getMigrationPath();

        $file = pathinfo($this->creator->create($attributes, $entity, $path), PATHINFO_FILENAME);

        $this->line("<info>Created Migration:</info> $file");
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
