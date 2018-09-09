<?php

namespace Eav\Console;

use Eav\Entity;
use Illuminate\Console\Command;
use Eav\Entity\Exporter as EntityExporter;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EntityExporterCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'eav:entity:export {entity : The name of the entity.}
        {--path= : The location where the expoterd file should be created.}
        {--C|count=100 : No of items that can be expoterd in a single query.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export the data.';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new controller creator command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

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

            $this->info("\nExporting data for `{$entityCode}` entity.");

            $path = $this->getExportPath($entityCode);
            (new EntityExporter($entity, $this->files, $this))->export($path);
            
            $this->info("\n\nData has been exported to : $path.");
        } catch (ModelNotFoundException $e) {
            $this->error("`{$entityCode}` entity doesn't exists.");
        }
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getExportPath($name)
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return $this->laravel->basePath().'/'.rtrim(trim($targetPath), '/').'/'. $name . '.csv';
        }

        return $this->laravel->storagePath().'/export/' . $name . '.csv';
    }
}
