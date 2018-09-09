<?php

namespace Eav\Migrations;

use Closure;
use League\Csv\Reader;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class AttributeMigrationCreator
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new migration creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Populate migration from string.
     *
     * @param  string  $attributes
     * @param  string  $entity
     * @param  string  $stub
     * @return [string, array]
     */
    public function createFromString($attributes, $entity, $path)
    {
        $attStub = $attStubR =  '';
        
        $attRemoveStubDefault = $this->getAttributeRemoveStub();

        $attributesProcessed = [];
        
        foreach (explode(',', $attributes) as $attribute) {
            $info = explode(':', $attribute);

            if (strlen($info[0]) > 50) {
                throw new \Exception("$info[0] : attribute_code is too long must be within 50");
            }

            $attStub .= $this->replaceAttrUpStub($this->generateDefaultsForString($info));
            $attStubR .= str_replace('ATTRIBUTECODE', $info[0], $attRemoveStubDefault);
            $attributesProcessed[] = $info[0];
        }

        $path = $this->create(
            $this->populateMigration($attStub, $attStubR, $entity, $this->getStub()),
            $entity,
            $path
        );

        return [$path, $attributesProcessed];
    }

    /**
     * Populate migration from source path.
     *
     * @param  string  $source
     * @param  string  $entity
     * @param  string  $stub
     * @return [string, array]
     */
    public function createFromSource($source, $entity, $path)
    {
        $reader = Reader::createFromPath($source, 'r');
        $reader->setHeaderOffset(0);
        $records = $reader->getRecords();

        $attStub = $attStubR =  '';
        
        $attRemoveStubDefault = $this->getAttributeRemoveStub();

        $attributesProcessed = [];

        foreach ($records as $offset => $record) {
            if (strlen($record['attribute_code']) > 50) {
                throw new \Exception("{$record['attribute_code']} : attribute_code is too long must be within 50");
            }

            $attStub .= $this->replaceAttrUpStub($record);
            $attStubR .= str_replace('ATTRIBUTECODE', $record['attribute_code'], $attRemoveStubDefault);
            $attributesProcessed[] = $record['attribute_code'];
        }

        $path = $this->create(
            $this->populateMigration($attStub, $attStubR, $entity, $this->getStub()),
            $entity,
            $path
        );

        return [$path, $attributesProcessed];
    }


    protected function replaceAttrUpStub($data)
    {
        $attAddStubDefault = $this->getAttributeAddStub();

        return str_replace([
            'ATTRIBUTECODE',
            'BACKENDCLASS',
            'BACKENDTYPE',
            'BACKENDTABLE',
            'FRONTENDCLASS',
            'FRONTENDTYPE',
            'FRONTENDLABEL',
            'SOURECCLASS',
            'DEFAULTVALUE',
            'ISREQUIRED',
            'ISFILTERABLE',
            'ISSEARCHABLE',
            'VALIDATIONCLASS'
        ], $data, $attAddStubDefault);
    }

    protected function generateDefaultsForString($intial)
    {
        return [
            'attribute_code' => $intial[0],
            'backend_class' => null,
            'backend_type' => isset($intial[1])?$intial[1]:'string',
            'backend_table' =>  null,
            'frontend_class' =>  null,
            'frontend_type' => 'text',
            'frontend_label' => ucwords(str_replace('_', ' ', $intial[0])),
            'source_class' =>  null,
            'default_value' => '',
            'is_required' => 0,
            'is_filterable' => 0,
            'is_searchable' => 0,
            'required_validate_class' =>  null
        ];
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $up
     * @param  string  $down
     * @param  string  $entity
     * @param  string  $stub
     * @return string
     */
    protected function populateMigration($up, $down, $entity, $stub)
    {
        $stub = str_replace('DummyClass', $this->getClassName("create_{$entity}_entity_attributes_".date('His')), $stub);
        
        $stub = str_replace(['ADDATTRIBUTE', 'REMOVEATTRIBUTE'], [$up, $down], $stub);

        $stub = str_replace('ENTITYCODE', $entity, $stub);

        return $stub;
    }

    /**
     * Create a new migration at the given path.
     *
     * @param  string  $stub
     * @param  string  $entity
     * @param  string  $path
     * @return string
     */
    protected function create($stub, $entity, $path)
    {
        $path = $this->getPath("create_{$entity}_entity_attributes_".date('His'), $path);

        $this->files->put($path, $stub);

        return $path;
    }

    /**
     * Get the migration stub file.
     *
     * @param  string  $table
     * @param  bool    $create
     * @return string
     */
    protected function getStub()
    {
        return $this->files->get($this->getStubPath()."/create.attribute.stub");
    }

    /**
     * Get the migration stub file.
     *
     * @param  string  $table
     * @param  bool    $create
     * @return string
     */
    protected function getAttributeAddStub()
    {
        return $this->files->get($this->getStubPath()."/attribute.add.stub");
    }
    
    /**
     * Get the migration stub file.
     *
     * @param  string  $table
     * @param  bool    $create
     * @return string
     */
    protected function getAttributeRemoveStub()
    {
        return $this->files->get($this->getStubPath()."/attribute.remove.stub");
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $attributes
     * @param  string  $entity
     * @param  string  $stub
     * @return string
     */
    protected function populateStub($attributes, $entity, $stub)
    {
        $stub = str_replace('DummyClass', $this->getClassName("create_{$entity}_entity_attributes_".date('His')), $stub);
        
        $attStub = $attStubR =  '';
        $attAddStubDefault = $this->getAttributeAddStub();
        $attRemoveStubDefault = $this->getAttributeRemoveStub();
        foreach (explode(',', $attributes) as $attribute) {
            $attStub .= str_replace('ATTRIBUTECODE', $attribute, $attAddStubDefault);
            $attStubR .= str_replace('ATTRIBUTECODE', $attribute, $attRemoveStubDefault);
        }
        
        $stub = str_replace(['ADDATTRIBUTE', 'REMOVEATTRIBUTE'], [$attStub, $attStubR], $stub);

        $stub = str_replace('ENTITYCODE', $entity, $stub);

        return $stub;
    }

    /**
     * Get the class name of a migration name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getClassName($name)
    {
        return Str::studly($name);
    }

    /**
     * Get the full path name to the migration.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        return $path.'/'.$this->getDatePrefix().'_'.$name.'.php';
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function getStubPath()
    {
        return __DIR__.'/stubs';
    }

    /**
     * Get the filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }
}
