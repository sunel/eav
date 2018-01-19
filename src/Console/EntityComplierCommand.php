<?php

namespace Eav\Console;

use Eav\Entity;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Eav\Flat\Entity\Complier as EntityComplier;
use Symfony\Component\Console\Input\InputOption;

class EntityComplierCommand extends Command
{

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'eav:compile:entity {entity : The name of the entity.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile Entity into Flat';
    
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

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
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();
        
        $this->files = $files;
        $this->composer = $composer;
    }
    
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $entity = $this->input->getArgument('entity');

        (new EntityComplier(Entity::findByCode($entity), $this->files))->compile();
		
		$this->info("`{$entity}` entity is compiled successfully and flat table is created.");
    }
}
