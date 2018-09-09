<?php

namespace Eav\Console;

use Eav\Entity;
use Illuminate\Console\Command;
use Eav\Flat\Entity\Updater as EntityUpdater;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FlatEntityUpdaterCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'eav:compile:updater {entity : The name of the entity.}
        {--C|count=100 : No of items that can be insert in a single query.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update date into Compile Entity Flat Table.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $entityCode = $this->input->getArgument('entity');

        try {
            $entity = Entity::findByCode($entityCode);

            $this->info("\nUpdating data for `{$entityCode}` entity.\n");

            (new EntityUpdater($entity, $this))->insert();
            
            $this->info("\nData has been updated.");
        } catch (ModelNotFoundException $e) {
            $this->error("`{$entityCode}` entity doesn't exists.");
        }
    }
}
