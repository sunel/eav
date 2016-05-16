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
    protected $type = 'Table';
	
}