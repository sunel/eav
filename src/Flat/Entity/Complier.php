<?php

namespace Eav\Flat\Entity;

use Eav\Entity;
use Illuminate\Support\Str;
use Eav\Migrations\SchemaParser;
use Eav\Migrations\SyntaxBuilder;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;

class Complier
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;
    
    protected $entity;

    protected $console;

    public function __construct(Entity $entity, Filesystem $files, Command $console)
    {
        $this->entity = $entity;
        $this->files = $files;
        $this->console = $console;
    }

    public function compile()
    {
        $this->createTable();
        $this->insertValues();
    }
    
    protected function insertValues()
    {
        $entity = app($this->entity->entity_class);

        $flatEntity = app($this->entity->entity_class);
    
        $flatEntity->baseEntity();

        $this->console->info("\t Updating `{$this->entity->entity_table}` flat table.");

        $entity->select('attr.*')->chunk(100, function ($chunk) use ($flatEntity) {
            $flatEntity->setUseFlat(true);
            $flatEntity->insert($chunk->toArray());
            $flatEntity->setUseFlat(false);
        });

        $this->console->info("\t Updated `{$this->entity->entity_table}` flat table.");
    }

    protected function createTable()
    {
        $this->console->info("\t Creating flat table for `{$this->entity->entity_table}`.");

        $path = $this->getPath($this->entity->entity_table.'_flat');
        
        $this->makeDirectory($path);
        
        $this->files->put($path, $this->compileMigrationStub());
        
        $this->files->requireOnce($path);
        
        $this->console->info("\t Migrating `{$this->entity->entity_table}` flat schema.");

        $this->runUp($path);
    }
    
    /**
     * Run "up" a migration instance.
     *
     * @param  string  $file
     * @param  int     $batch
     * @param  bool    $pretend
     * @return void
     */
    protected function runUp($file)
    {
        // First we will resolve a "real" instance of the migration class from this
        // migration file name. Once we have the instances we can run the actual
        // command such as "up" or "down", or we can just simulate the action.
        $migration = $this->resolve($file);

        $migration->up();

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a migration
        // in the application. A migration repository keeps the migrate order.
        //$this->repository->log($file, $batch);

        //$this->note("<info>Migrated:</info> $file");
    }
    
    /**
    * Resolve a migration instance from a file.
    *
    * @param  string  $file
    * @return object
    */
    public function resolve($file)
    {
        $file = basename($file, ".php");

        $class = Str::studly($file);

        return new $class;
    }

    protected function buildSchema()
    {
        $table = $this->entity->describe()->map(function ($attribute) {
            if ($attribute['COLUMN_KEY'] == 'PRI') {
                $schema = "{$attribute['COLUMN_NAME']}:{$this->getColumn($attribute['DATA_TYPE'])}:unsigned";
            } else {
                if ($attribute['DATA_TYPE'] == 'decimal') {
                    $schema = "{$attribute['COLUMN_NAME']}:{$this->getColumn($attribute['DATA_TYPE'])}({$attribute['NUMERIC_PRECISION']} , {$attribute['NUMERIC_SCALE']})";
                } elseif ($attribute['DATA_TYPE'] == 'int') {
                    $schema = "{$attribute['COLUMN_NAME']}:{$this->getColumn($attribute['DATA_TYPE'])}";
                    if (!Str::contains($attribute['COLUMN_TYPE'], 'unsigned')) {
                        $schema .= "({$attribute['NUMERIC_PRECISION']})";
                    }
                } elseif ($attribute['DATA_TYPE'] == 'varchar') {
                    $schema = "{$attribute['COLUMN_NAME']}:{$this->getColumn($attribute['DATA_TYPE'])}({$attribute['CHARACTER_MAXIMUM_LENGTH']})";
                } else {
                    $schema = "{$attribute['COLUMN_NAME']}:{$this->getColumn($attribute['DATA_TYPE'])}";
                }
            
                if (Str::contains($attribute['COLUMN_TYPE'], 'unsigned')) {
                    $schema .= ":unsigned";
                }
                
                if ($attribute['IS_NULLABLE'] != 'NO') {
                    $schema .= ":nullable";
                }
                
                if ($attribute['COLUMN_DEFAULT'] != null) {
                    $schema .= ":default('{$attribute['COLUMN_DEFAULT']}')";
                }
            }
            
            return $schema;
        });
        
        
        $attributes = $this->collectAttributes()->where('backend_type', '!=', 'static')->get()->map(function ($attribute) {
            $schema = "{$attribute->getAttributeCode()}";
            
            if ($attribute->getBackendType() == 'decimal') {
                $schema .= ":{$this->getColumn($attribute->getBackendType())}(12 , 4)";
            } else {
                $schema .= ":{$this->getColumn($attribute->getBackendType())}";
            }
            
            $schema .= ":nullable";
            
            if ($defaultValue = $attribute->getDefaultValue()) {
                $schema .= ":default('$defaultValue')";
            }
            
            return $schema;
        });
        
        $this->console->info("\t Found {$attributes->count()} attributes.");
        
        $schema = (new SchemaParser)->parse($table->implode(',').','.$attributes->implode(','));
        
        return (new SyntaxBuilder)->create($schema);
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }
    /**
     * Get the path to where we should store the migration.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        return base_path() . '/database/migrations/eav/' . $name . '.php';
    }
   
    /**
     * Compile the migration stub.
     *
     * @return string
     */
    protected function compileMigrationStub()
    {
        $stub = $this->files->get(__DIR__ . '/../../Migrations/stubs/migration.stub');
        $this->replaceClassName($stub)
            ->replaceSchema($stub)
            ->replaceTableName($stub);
        return $stub;
    }
    /**
     * Replace the class name in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceClassName(&$stub)
    {
        $className = ucwords(camel_case($this->entity->entity_table.'_flat'));
        $stub = str_replace('{{class}}', $className, $stub);
        return $this;
    }
    /**
     * Replace the table name in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceTableName(&$stub)
    {
        $table = $this->entity->entity_table.'_flat';
        $stub = str_replace('{{table}}', $table, $stub);
        return $this;
    }
    /**
     * Replace the schema for the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceSchema(&$stub)
    {
        $schema = $this->buildSchema();
        $stub = str_replace(['{{schema_up}}', '{{schema_down}}'], $schema, $stub);
        return $this;
    }

    
    protected function getColumn($type)
    {
        switch ($type) {
            case 'int':
                return 'integer';
                break;
            case 'timestamp':
                return 'timestamp';
                break;
            case 'datetime':
                return 'dateTime';
                break;
            case 'decimal':
                return 'decimal';
                break;
            case 'varchar':
            default:
                return 'string';
                break;
        }
    }

    protected function collectAttributes()
    {
        return $this->entity->attributes();
    }
}
