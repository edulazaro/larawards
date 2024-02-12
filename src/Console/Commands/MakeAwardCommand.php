<?php

namespace EduLazaro\Larawards\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeAwardCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:award {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new award';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Award';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = '/../../../Stubs/award.stub';
        return __DIR__ . $stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Awards';
    }

    /**
     * @param string $stub
     * @param string $name
     * 
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $awardClass = $name;

        $awardClassSegments = explode('\\', $awardClass);
        $awardClass = end($awardClassSegments);

        $id =  strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', basename(str_replace('\\', '/', $name))));

        $stub = str_replace('{{award_class}}', $awardClass, $stub);
        return str_replace('{{id}}', $id, $stub);
    }
}