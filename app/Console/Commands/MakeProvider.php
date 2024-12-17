<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MakeProvider extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nampx:provider {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Khởi tạo Provider.';

    protected $type = 'Provider';

    protected function getStub()
    {
        return base_path('stubs/provider.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $class = $this->getNameInput();
        $modifiedClassName = str_replace("ServiceProvider", "", $class);

        return $rootNamespace . "\Providers\\" . $modifiedClassName;
    }

    protected function replaceClass($stub, $name)
    {
        $class = $this->getNameInput();
        $modifiedClassName = str_replace("ServiceProvider", "", $class);

        $providerClass = "App\Providers\\" . $modifiedClassName . '\\' . $class;

        $configPath = config_path('app.php');
        $config = file_get_contents($configPath);

        if (strpos($config, $providerClass) === false) {
            $config = str_replace(
                "'providers' => [",
                "'providers' => [\n        $providerClass::class,\n",
                $config
            );
            file_put_contents($configPath, $config);
            $this->info("Provider $providerClass added.");
        } else {
            $this->info("Provider $providerClass is already registered.");
        }

        // Do string replacement
        return str_replace('{{name}}', $modifiedClassName, $stub);
    }

}
