<?php

namespace Monoland\Platform\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PlatformModuleSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:seed
        {class?}
        {--module=}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database in module with records';

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
            $this->error('The x module not exists.');
            return;
        }

        $className = str($module->namespace . ' ' . $module->name)->studly()->toString() . '\\Seeders\\' . ($this->argument('class') ?: 'DatabaseSeeder');

        if (class_exists($className)) {
            $this->call('db:seed', [
                '--class' => $className,
                '--database' => $module->connection
            ]);
        } else {
            $this->error('The class ' . $className . ' not exists.');
        }
    }
}
