<?php

namespace Siruhay\Platform\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PlatformMakeResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-resource
        {name}
        {--collection}
        {--show}
        {--model=}
        {--module=}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make monosoft new resource in module';

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
        if ($this->option('collection') || str($this->argument('name'))->contains('Collection')) {
            $stubFile = __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'resource-collection.stub';
        } elseif ($this->option('show') || str($this->argument('name'))->contains('ShowResource')) {
            $stubFile = __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'resource-show.stub';
        } else {
            $stubFile = __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'resource.stub';
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
                'Http' .
                DIRECTORY_SEPARATOR .
                'Resources'
        );

        $filename = $this->argument('name') . '.php';
        $fileOutput = $filepath . DIRECTORY_SEPARATOR . $filename;

        /** CHECK FILE IS EXISTS */
        if ($fileSystem->exists($fileOutput)) {
            $this->error('The resource already exists.');
            return;
        }

        /** CREATE FILE BASE ON STUB */
        $fileSystem->put(
            $fileOutput,
            $this->getStubContents($module, $fileSystem->get($stubFile))
        );

        /** MESSAGE FILE CREATING IS COMPLETED */
        $this->info('The ' . $this->argument('name') . ' model has been created.');
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
            '$CLASSNAME$',
            '$NAMESPACE$',
            '$MODEL$',
            '$MODEL_LOWER$',
            '$MODEL_RESOURCE$',
            '$MODULE$',
            '$MODULE_LOWER$',
        ];

        $replacements = [
            str($this->argument('name'))->studly()->toString(),
            str($module->namespace)->studly()->toString(),
            str($this->option('model'))->studly()->toString(),
            str($this->option('model'))->studly()->replace(str($module->name)->studly()->toString(), '')->lower()->toString(),
            str($this->option('model'))->studly()->replace(str($module->name)->studly()->toString(), '')->toString() . 'Resource',
            str($module->name)->studly()->toString(),
            str($module->name)->lower()->toString(),
        ];

        return str_replace(
            $searches,
            $replacements,
            $stubFile
        );
    }
}
