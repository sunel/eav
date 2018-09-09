<?php

namespace Eav\Console;

use Eav\Entity;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ActivateFlatEntityCommand extends Command
{

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'eav:flat:entity {entity : The name of the entity.} {--E|enable=true}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable Flat Table for Entity.';
    
    
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

            if ($this->option('enable') == 'true') {
                $entity->is_flat_enabled = true;
                $entity->save();
                $this->info("Enabling flat table for `{$entityCode}`.");
            } else {
                $entity->is_flat_enabled = false;
                $entity->save();
                $this->info("Disabling flat table for `{$entityCode}`.");
            }
        } catch (ModelNotFoundException $e) {
            $this->error("`{$entityCode}` entity doesn't exists.");
        }
    }
}
