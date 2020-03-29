<?php

namespace Eav\Flat\Entity;

use Eav\Entity;
use Illuminate\Support\Str;
use Eav\Migrations\SchemaParser;
use Eav\Migrations\SyntaxBuilder;
use Illuminate\Support\Collection;
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
        $this->console->info("\t Creating flat table for `{$this->entity->code()}`.");

        $path = $this->getPath($this->entity->entityTableName().'_flat');

        $this->console->info("\t in {$path}");
        
        $this->makeDirectory($path);
        
        $this->files->put($path, $this->compileMigrationStub());
        
        $this->files->requireOnce($path);
        
        $this->console->info("\t Migrating `{$this->entity->code()}` flat schema.");

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
    }
    
    /**
    * Resolve a migration instance from a file.
    *
    * @param  string  $file
    * @return object
    */
    protected function resolve($file)
    {
        $file = basename($file, ".php");

        $class = Str::studly($file);

        return new $class;
    }

    protected function buildSchema()
    {
        $table = $this->describe($this->entity->entityTableName())->map(function ($attribute) {
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
                
                if ($attribute['COLUMN_DEFAULT'] != null && $attribute['COLUMN_DEFAULT'] != "NULL") {
                    $schema .= ":default('{$attribute['COLUMN_DEFAULT']}')";
                }
            }
            
            return $schema;
        });

        $attributes = Collection::make([]);

        $tableCache = Collection::make([]);

        $this->collectAttributes()->chunk(500, function ($chunk, $page) use ($attributes, $tableCache) {
            $chunk->map(function ($attribute) use ($attributes, $tableCache) {                
                $table = $tableCache->get($attribute->backendTable(), function () use ($attribute, $tableCache) {
                    $key = $attribute->backendTable();
                    return $tableCache->put($key, $this->describe($key, function ($query) {
                        return $query->where('COLUMN_NAME', 'value');
                    })->first())->get($key);
                });

                $schema = $attribute->getAttributeCode();

                $backendTable = $attribute->getBackendType();
                
                if ($backendTable == 'char' || $backendTable == 'string') {
                    $schema .= ":{$backendTable}({$table['CHARACTER_MAXIMUM_LENGTH']})";
                } elseif (in_array($backendTable, ['decimal', 'double', 'float', 'unsignedDecimal'])) {
                    $schema .= ":{$backendTable}({$table['NUMERIC_PRECISION']}, {$table['NUMERIC_SCALE']})";
                } else {
                    $schema .= ":{$backendTable}";
                }
                
                $schema .= ":nullable";
                
                if ($defaultValue = $attribute->getDefaultValue()) {
                    $schema .= ":default('$defaultValue')";
                }
                
                $attributes->push($schema);
            });
            return false;
        });
        
        $this->console->info("\t Found {$attributes->count()} attributes.");
        
        $schema = (new SchemaParser)->parse($table->implode(',').','.$attributes->implode(','));
        
        return (new SyntaxBuilder)->create($schema);
    }

    /**
     * Describe the table structure, this is used while creating flat table.
     *
     * @return Illuminate\Support\Collection
     */
    public function describe($table, $clouser = null)
    {
        if (is_null($clouser)) {
            $clouser = function ($query) {
                return $query;
            };
        }

        $connection = \DB::connection();
        
        $database = $connection->getDatabaseName();

        $table = $connection->getTablePrefix().$table;
        
        $result = \DB::table(config('eav.information_schema_prefix', '').'information_schema.columns')
                ->where('table_schema', $database)
                ->where('table_name', $table)
                ->where($clouser)
                ->get();
                
        return new Collection(json_decode(json_encode($result), true));
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
        if (! is_null($targetPath = $this->console->option('path'))) {
            return $this->console->getLaravel()->basePath().'/'.rtrim(trim($targetPath), '/').'/'. $name . '.php';
        }

        return $this->console->getLaravel()->databasePath().'/migrations/eav/' . $name . '.php';
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
        $className = ucwords(camel_case($this->entity->entityTableName().'_flat'));
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
        $table = $this->entity->entityTableName().'_flat';
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
        return $this->entity->attributes()
            ->whereNotIn('backend_type', ['static', '']);
    }
}
