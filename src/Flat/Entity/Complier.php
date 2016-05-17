<?php

namespace Eav\Flat\Entity;

use Eav\Entity;
use Eav\Migrations\SchemaParser;
use Eav\Migrations\SyntaxBuilder;
use Illuminate\Filesystem\Filesystem;

class Complier
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;
    
    protected $entity;

    public function __construct(Entity $entity, Filesystem $files)
    {
        $this->entity = $entity;
        $this->files = $files;
    }

    public function compile()
    {
        $this->createTable();
        //$this->insertValues();
    }

    protected function createTable()
    {
        $this->buildSchema();
    }

    protected function buildSchema()
    {
        $table = $this->entity->describe()->map(function ($attribute) {
            
            if ($attribute['Field'] == 'id') {
                $schema =  "id:increment";
            } else {
                $schema = "{$attribute['Field']}:{$attribute['Type']}";
                
                if ($attribute['Null'] != 'NO') {
                    $schema .= ":nullable";
                }
                
                if ($attribute['Default'] != null) {
                    $schema .= ":default('{$attribute['Default']}')";
                }
            }
            
            return $schema;
            
        })->all();
        
        
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
            
        })->all();
        
        
        $schema = (new SchemaParser)->parse(implode(',', $table).','.implode(',', $attributes));
        
        return (new SyntaxBuilder)->create($schema, ['action' => 'create', 'table' => $this->entity->entity_table.'_flat']);
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
        return base_path() . '/database/migrations/eav/' . date('Y_m_d_His') . '_' . $name . '.php';
    }
   
    /**
     * Compile the migration stub.
     *
     * @return string
     */
    protected function compileMigrationStub()
    {
        $stub = $this->files->get(__DIR__ . '/../stubs/migration.stub');
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
        $className = ucwords(camel_case($this->argument('name')));
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
        $table = $this->meta['table'];
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
        if ($schema = $this->option('schema')) {
            $schema = (new SchemaParser)->parse($schema);
        }
        $schema = (new SyntaxBuilder)->create($schema, $this->meta);
        $stub = str_replace(['{{schema_up}}', '{{schema_down}}'], $schema, $stub);
        return $this;
    }

    
    protected function getColumn($type)
    {
        switch ($type) {
            case 'int':
                return 'integer';
                break;
            case 'datetime':
                return 'dateTime';
                break;
            case 'varchar':
            default:
                return 'string';
                break;
        }
    }

    protected function collectAttributes()
    {
        return $this->entity->eavAttributes();
    }
}
