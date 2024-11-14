<?php

namespace Monoland\Platform\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class PlatformModuleMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:migrate 
        {module}
        {--fresh}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run monosoft database migration in module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /** GET MODULE INFO */
        $modules = Cache::get('modules');

        if (is_array($modules) && array_key_exists($this->argument('module'), $modules)) {
            $module = $modules[$this->argument('module')];
        } else {
            $this->error('The module not exists.');
            return;
        }

        $modulePath = DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . str($module->name)->lower() . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';

        if ($this->option('fresh')) {
            $files = glob(base_path($modulePath) . DIRECTORY_SEPARATOR . '*.php');

            foreach ($files as $file) {
                $migrationName = str($file)->after("database/migrations/")->before(".php")->toString();

                // remove migration record
                DB::connection($module->connection)
                    ->table('migrations')
                    ->where('migration', $migrationName)
                    ->delete();

                // drop table
                $tableName = str($migrationName)->after("_create_")->before("_table")->toString();

                DB::connection($module->connection)
                    ->statement('DROP TABLE IF EXISTS ' . $tableName);
            }
        }

        $this->call('migrate', [
            '--database' => $module->connection,
            '--path' => $modulePath
        ]);
    }
}
