<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MakeRouter extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nampx:router {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Khởi tạo Router.';

    protected $type = 'Router';

    protected function getStub()
    {
        return base_path('stubs/router.stub');
    }

    protected function getPath($name)
    {
        $class = $this->getNameInput();
        $modifiedClassName = Str::lower(str_replace("App", "", $class));

        return base_path() . '/routes/app/' . $modifiedClassName . '.php';
    }

    protected function replaceClass($stub, $name)
    {
        $class = $this->getNameInput();
        $modifiedClassName = str_replace("App", "", $class);
        // Do string replacement
        return str_replace('{{ name }}', $modifiedClassName, $stub);
    }
}
