<?php

namespace Siruhay\Platform\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PlatformMakeReplica extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-replica
        {name}
        {--all}
        {--origin=}
        {--origin-module=}
        {--parent=}
        {--module=}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make monosoft new model-replica in module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('module')) {
            $this->error('Please input module name');
            return;
        }

        /** SET FILESYSTEM */
        $fileSystem = $this->laravel['files'];

        /** SET STUB FILE */
        $stubFile = __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'model-replica.stub';

        /** CHECK STUB IS EXISTS */
        if (!$fileSystem->exists($stubFile)) {
            $this->error('The stub file not exists.');
            return;
        }

        /** GET MODULE INFO */
        $modules = Cache::get('modules');

        if (is_array($modules) && array_key_exists($this->option('module'), $modules)) {
            $module = $modules[$this->option('module')];
        } else {
            $this->error('The module not exists.');
            return;
        }

        if (is_array($modules) && $this->option('origin-module') && array_key_exists($this->option('origin-module'), $modules)) {
            $originModule = $modules[$this->option('origin-module')];
        } else {
            $originModule = $module;
        }

        /** SET FILE OUTPUT */
        $filepath = base_path(
            'modules' .
                DIRECTORY_SEPARATOR .
                str($module->name)->lower() .
                DIRECTORY_SEPARATOR .
                'src' .
                DIRECTORY_SEPARATOR .
                'Models'
        );

        $modelname = $this->argument('name');
        $classname = $module->name . $modelname;
        $filename = $classname . '.php';
        $fileOutput = $filepath . DIRECTORY_SEPARATOR . $filename;

        /** CHECK MODEL IS EXISTS */
        if ($fileSystem->exists($fileOutput)) {
            $this->error('The model already exists.');
            return;
        }

        /** CREATE FILE BASE ON STUB */
        $fileSystem->put(
            $fileOutput,
            $this->getStubContents($module, $originModule, $fileSystem->get($stubFile))
        );

        /** MESSAGE FILE CREATING IS COMPLETED */
        $this->info('The ' . $this->argument('name') . ' model has been created.');

        /** CHECK ALL-FLAG */
        if ($this->option('all')) {
            /** MAKE RESOURCE */
            $this->call('module:make-resource', [
                'name' => $modelname . 'Resource',
                '--model' => $classname,
                '--module' => $this->option('module')
            ]);

            /** MAKE COLLECTION */
            $this->call('module:make-resource', [
                'name' => $modelname . 'Collection',
                '--collection' => true,
                '--model' => $classname,
                '--module' => $this->option('module')
            ]);

            /** MAKE SHOW RESOURCE */
            $this->call('module:make-resource', [
                'name' => $modelname . 'ShowResource',
                '--show' => true,
                '--model' => $classname,
                '--module' => $this->option('module')
            ]);

            /** MAKE CONTROLLER */
            $this->call('module:make-controller', array_merge([
                'name' => $classname . 'Controller',
                '--model' => $classname,
                '--module' => $this->option('module')
            ], $this->option('parent') ? [
                '--parent' => $this->option('parent'),
            ] : []));

            /** MAKE POLICY */
            $this->call('module:make-policy', [
                'name' => $classname . 'Policy',
                '--model' => $classname,
                '--module' => $this->option('module')
            ]);
        }
    }

    /**
     * Get the contents of the specified stub file by given stub name.
     *
     * @param $stub
     *
     * @return string
     */
    protected function getStubContents($module, $originModule, $stubFile): string
    {
        $searches = [
            '$CLASSNAME$',
            '$NAMESPACE$',
            '$MODEL_LOWER$',
            '$MODULE$',
            '$MODULE_LOWER$',
            '$ORIGIN_NAMESPACE$',
            '$ORIGIN_MODEL$',
            '$ORIGIN_MODULE$'
        ];

        $replacements = [
            str($module->name . $this->argument('name'))->studly()->toString(),
            str($module->namespace)->studly()->toString(),
            str($this->argument('name'))->studly()->replace(str($module->name)->studly()->toString(), '')->lower()->toString(),
            str($module->name)->studly()->toString(),
            str($module->name)->lower()->toString(),
            str($originModule->namespace)->studly()->toString(),
            str($this->option('origin'))->studly()->toString(),
            str($originModule->name)->studly()->toString(),
        ];

        return str_replace(
            $searches,
            $replacements,
            $stubFile
        );
    }
}
