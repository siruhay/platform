<?php

namespace Siruhay\Platform\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PlatformMakeModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-model
        {model}
        {--all}
        {--ignore-migration}
        {--parent=}
        {--module=}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make monosoft new model in module';

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
        if ($this->option('parent')) {
            $stubFile = __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'model-parent.stub';
        } else {
            $stubFile = __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'model.stub';
        }

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

        /** CHECK FOLDER EXISTS */
        if (!$fileSystem->exists($filepath)) {
            $fileSystem->makeDirectory($filepath);
        }

        $modelname = $this->argument('model');
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
            $this->getStubContents($module, $fileSystem->get($stubFile))
        );

        /** MESSAGE FILE CREATING IS COMPLETED */
        $this->info('The ' . $classname . ' model has been created.');

        /** CHECK ALL-FLAG */
        if ($this->option('all')) {
            if (!$this->option('ignore-migration')) {
                /** MAKE MIGRATION */
                $this->call('module:make-migration', [
                    'name' => 'Create' . str($classname)->plural()->toString(),
                    '--module' => $this->option('module')
                ]);
            }

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
    protected function getStubContents($module, $stubFile): string
    {
        $searches = [
            '$CONNECTION$',
            '$NAMESPACE$',
            '$NAMESPACE_LOWER$',
            '$MODEL$',
            '$MODEL_LOWER$',
            '$MODEL_PLURAL$',
            '$PARENT_MODEL$',
            '$MODULE$',
            '$MODULE_LOWER$',
            '$MODULE_SLUG$',
        ];

        $replacements = [
            $module->connection,
            str($module->namespace)->studly()->toString(),
            str($module->namespace)->lower()->toString(),
            str($this->argument('model'))->studly()->toString(),
            str($this->argument('model'))->lower()->toString(),
            str($this->argument('model'))->plural()->lower()->toString(),
            str($this->option('parent'))->studly()->toString(),
            str($module->name)->studly()->toString(),
            str($module->name)->lower()->toString(),
            str($module->namespace . '-' . $module->name)->slug()->toString(),
        ];

        return str_replace(
            $searches,
            $replacements,
            $stubFile
        );
    }
}
