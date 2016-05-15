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
     * The registered post create hooks.
     *
     * @var array
     */
    protected $postCreate = [];

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
     * @param  string  $name
     * @param  string  $path
     * @param  string  $table
     * @param  bool    $create
     * @return string
     */
    public function create($name, $entity, $path)
    {
        $path = $this->getPath(md5($name.'attributes'.$entity), $path);

        // First we will get the stub file for the migration, which serves as a type
        // of template for the migration. Once we have those we will populate the
        // various place-holders, save the file, and run the post create event.
        $stub = $this->getStub();

        $this->files->put($path, $this->populateStub($name, $entity, $stub));

        $this->firePostCreateHooks();

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
     * @param  string  $name
     * @param  string  $stub
     * @param  string  $table
     * @return string
     */
    protected function populateStub($name, $entity, $stub)
    {
        $stub = str_replace('DummyClass', $this->getClassName(md5($name.'attributes'.$entity)), $stub);
        
        $attStub = $attStubR =  '';
        $attAddStubDefault = $this->getAttributeAddStub();
        $attRemoveStubDefault = $this->getAttributeRemoveStub();
        foreach (explode(',', $name) as $attribute) {
            $attStub .= str_replace('ATTRIBUTECODE', $attribute, $attAddStubDefault);
            $attStubR .= str_replace('ATTRIBUTECODE', $attribute, $attRemoveStubDefault);
        }
        
        $stub = str_replace('ADDATTRIBUTE', $attStub, $stub);
        $stub = str_replace('REMOVEATTRIBUTE', $attStubR, $stub);
        
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
        return 'Entity'.Str::studly($name);
    }

    /**
     * Fire the registered post create hooks.
     *
     * @return void
     */
    protected function firePostCreateHooks()
    {
        foreach ($this->postCreate as $callback) {
            call_user_func($callback);
        }
    }

    /**
     * Register a post migration create hook.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function afterCreate(Closure $callback)
    {
        $this->postCreate[] = $callback;
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
        return $path.'/'.$this->getDatePrefix().'_entity_'.$name.'.php';
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
