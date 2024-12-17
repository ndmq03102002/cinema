<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Log;

class MakeService extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nampx:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Khởi tạo Service.';

    protected $type = 'Service';

    protected function getStub()
    {
        return base_path('stubs/service.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $serviceName = $this->getNameInput();
        return $rootNamespace . "\Services\\" . $serviceName;
    }

    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        // Do string replacement
        return str_replace('{{service_name}}', $class, $stub);
    }
}
