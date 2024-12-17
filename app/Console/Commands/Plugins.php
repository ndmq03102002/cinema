<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class Plugins extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nampx:plugins {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo plugin bao gồm: Controller, Modal, Services, Interface, Repository';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $providerName = $this->getNameInput() . 'ServiceProvider';
        $serviceName = $this->getNameInput() . 'Service';
        $interfaceName = $this->getNameInput() . 'Interface';
        $repositoryName = $this->getNameInput() . 'Repository';
        $routerName = $this->getNameInput();
        $controllerName = $this->getNameInput() . 'Controller';
        $RequestName = $this->getNameInput() . 'Request';

        Artisan::call('nampx:provider', ['name' => $providerName]);
        Artisan::call('nampx:service', ['name' => $serviceName]);
        Artisan::call('nampx:interface', ['name' => $interfaceName]);
        Artisan::call('nampx:repository', ['name' => $repositoryName]);
        Artisan::call('nampx:router', ['name' => $routerName]);
        Artisan::call('make:controller', ['name' => 'Api/' . $controllerName, '--resource' => true]);
        Artisan::call('make:request', ['name' => $RequestName]);

        $this->info("Plugins" . $this->getNameInput() . "added.");
    }

    protected function getStub()
    {
        // TODO: Implement getStub() method.
    }
}
