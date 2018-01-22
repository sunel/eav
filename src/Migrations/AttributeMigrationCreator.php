<?php

namespace Eav\Migrations;

use Closure;
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
     * Create a new migration at the given path.
     *
     * @param  string  $attributes
     * @param  string  $entity
     * @param  string  $path
     * @return string
     */
    public function create($attributes, $entity, $path)
    {
        $path = $this->getPath("create_{$entity}_entity_attributes_".date('His'), $path);

        $this->files->put($path, $this->populateStub($attributes, $entity, $this->getStub()));

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
