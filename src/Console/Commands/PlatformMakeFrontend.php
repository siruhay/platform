<?php

namespace Simasten\Platform\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PlatformMakeFrontend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-frontend
        {name}
        {--blank}
        {--parent=}
        {--module=}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make monosoft frontend-page in module';

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

        /** GET MODULE INFO */
        $modules = Cache::get('modules');

        if (is_array($modules) && array_key_exists($this->option('module'), $modules)) {
            $module = $modules[$this->option('module')];
        } else {
            $this->error('The module not exists.');
            return;
        }

        $parentDir  = $this->option('parent') ? str($this->option('parent'))->lower()->toString() : null;
        $directory  = $parentDir ? $parentDir . '-' . str($this->argument('name'))->lower()->toString() : str($this->argument('name'))->lower()->toString();

        /** DIRECTORY */
        $dirPath    = base_path(
            'modules' . DIRECTORY_SEPARATOR . 
            str($module->name)->lower() . DIRECTORY_SEPARATOR . 
            'frontend' . DIRECTORY_SEPARATOR . 
            'pages' . DIRECTORY_SEPARATOR . 
            $directory
        );
        
        if (!$fileSystem->exists($dirPath)) {
            $fileSystem->makeDirectory($dirPath); 
        }

        /** BLANK PAGE */
        if ($this->option('blank')) {
            /** SET STUB FILE */
            $stubFile   = __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'page-index-blank.stub';
            
            /** FILE */
            $fileOutput = $dirPath . DIRECTORY_SEPARATOR . 'index.vue';
        } else {
            /** DIRECTORY */
            $crudPath   = base_path(
                'modules' . DIRECTORY_SEPARATOR . 
                str($module->name)->lower() . DIRECTORY_SEPARATOR . 
                'frontend' . DIRECTORY_SEPARATOR . 
                'pages' . DIRECTORY_SEPARATOR . 
                $directory . DIRECTORY_SEPARATOR . 
                'crud'
            );
            
            if (!$fileSystem->exists($crudPath)) {
                $fileSystem->makeDirectory($crudPath); 
            }

            /** FILE */
            $fileOutput = [
                $dirPath . DIRECTORY_SEPARATOR . 'index.vue',
                $dirPath . DIRECTORY_SEPARATOR . 'crud' . DIRECTORY_SEPARATOR . 'create.vue',
                $dirPath . DIRECTORY_SEPARATOR . 'crud' . DIRECTORY_SEPARATOR . 'data.vue',
                $dirPath . DIRECTORY_SEPARATOR . 'crud' . DIRECTORY_SEPARATOR . 'edit.vue',
                $dirPath . DIRECTORY_SEPARATOR . 'crud' . DIRECTORY_SEPARATOR . 'show.vue',
            ];
            
            if ($this->option('parent')) {
                /** SET STUB FILE */
                $stubFile = [
                    __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'page-index-parent.stub',
                    __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'page-create.stub',
                    __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'page-data.stub',
                    __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'page-edit.stub',
                    __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'page-show.stub'
                ];
            } else {
                /** SET STUB FILE */
                $stubFile = [
                    __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'page-index.stub',
                    __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'page-create.stub',
                    __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'page-data.stub',
                    __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'page-edit.stub',
                    __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'page-show.stub'
                ];
            }
        }

        if (is_array($fileOutput)) {
            foreach ($fileOutput as $index => $filestub) {
                if ($fileSystem->exists($filestub)) {
                    continue;
                }

                /** CREATE FILE BASE ON STUB */
                $fileSystem->put(
                    $filestub,
                    $this->getStubContents($module, $fileSystem->get($stubFile[$index]))
                );
            }
        } else {
            /** CREATE FILE BASE ON STUB */
            $fileSystem->put(
                $fileOutput,
                $this->getStubContents($module, $fileSystem->get($stubFile))
            );
        }

        /** MESSAGE FILE CREATING IS COMPLETED */
        $this->info('The ' . $this->argument('name') . ' has been created.');
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
            '$MODULE_LOWER$',
            '$MODEL_LOWER$',
            '$PARENT_LOWER$',
        ];

        $replacements = [
            str($module->name)->lower()->toString(),
            str($this->argument('name'))->lower()->toString(),
            str($this->option('parent'))->lower()->toString(),
        ];

        return str_replace(
            $searches,
            $replacements,
            $stubFile
        );
    }
}
