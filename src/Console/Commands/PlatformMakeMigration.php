<?php

namespace Simasten\Platform\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PlatformMakeMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-migration
        {name}
        {--module=}
        {--create=}
        {--table=}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make monosoft new migration in module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('module')) {
            $this->error('Please input module name');
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
        $folderpath = 'modules' . DIRECTORY_SEPARATOR . str($module->name)->lower() . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';

        return $this->call('make:migration', [
            'name' => $this->argument('name'),
            '--create' => $this->option('create'),
            '--table' => $this->option('table'),
            '--path' => $folderpath,
        ]);
    }
}
