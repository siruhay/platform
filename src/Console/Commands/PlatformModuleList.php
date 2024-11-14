<?php

namespace Monoland\Platform\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PlatformModuleList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all modules in monosoft platform.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modules = [];
        $cacheModules = Cache::get('modules') ?: [];

        foreach ($cacheModules as $module) {
            array_push($modules, [
                $module->namespace,
                $module->name,
                $module->disabled ? 'true' : 'false',
                $module->priority,
                $module->connection,
                $module->directory
            ]);
        }

        array_multisort(array_column($modules, 3), SORT_ASC, $modules);

        $this->table(
            ['Namespace', 'Name', 'Disabled', 'Priority', 'Connection', 'Path'],
            $modules
        );
    }
}
