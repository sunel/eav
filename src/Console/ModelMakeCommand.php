<?php

namespace Eav\Console;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ModelMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'eav:make:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eav Eloquent model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/eav.model.stub';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $entity = $this->option('entity');

        $stub = $this->replaceNamespace($stub, $name)
                ->replaceClass($stub, $name);
        return $this->replaceEntity($stub, $entity);
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceEntity($stub, $name)
    {
        return str_replace('DummyEntity', $name, $stub);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['entity', 'e', InputOption::VALUE_REQUIRED, 'Entity for the model.'],
        ];
    }
}
