<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Log;

class MakeInterface extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nampx:interface {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Khởi tạo Interface.';

    protected $type = 'Interface';

    protected function getStub()
    {
        return base_path('stubs/interface.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $class = $this->getNameInput();
        $modifiedClassName = str_replace("Interface", "", $class);

        return $rootNamespace . "\Repositories\\" . $modifiedClassName;
    }

    protected function replaceClass($stub, $name)
    {
        $class = $this->getNameInput();
        $modifiedClassName = str_replace("Interface", "", $class);

        // Do string replacement
        return str_replace('{{name}}', $modifiedClassName, $stub);
    }
}
