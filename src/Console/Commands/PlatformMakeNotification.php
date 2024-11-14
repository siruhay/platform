<?php

namespace Monoland\Platform\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PlatformMakeNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-notification
        {name}
        {--module=}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make monosoft new notification in module';

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
        $stubFile = __DIR__ . DIRECTORY_SEPARATOR . 'system-stubs' . DIRECTORY_SEPARATOR . 'notification.stub';

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
                'Notifications'
        );

        /** CHECK FOLDER EXISTS */
        if (!$fileSystem->exists($filepath)) {
            $fileSystem->makeDirectory($filepath);
        }

        $filename = $this->argument('name') . '.php';
        $fileOutput = $filepath . DIRECTORY_SEPARATOR . $filename;

        /** CHECK FILE IS EXISTS */
        if ($fileSystem->exists($fileOutput)) {
            $this->error('The import already exists.');
            return;
        }

        /** CREATE FILE BASE ON STUB */
        $fileSystem->put(
            $fileOutput,
            $this->getStubContents($module, $fileSystem->get($stubFile))
        );

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
            '$CLASSNAME$',
            '$NAMESPACE$',
            '$MODULE$',
            '$DATATYPE$',
        ];

        $replacements = [
            str($this->argument('name'))->studly()->toString(),
            str($module->namespace)->studly()->toString(),
            str($module->name)->studly()->toString(),
            str($this->argument('name'))->snake('-')->toString(),
        ];

        return str_replace(
            $searches,
            $replacements,
            $stubFile
        );
    }
}
