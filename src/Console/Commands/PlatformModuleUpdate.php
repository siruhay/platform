<?php

namespace Monoland\Platform\Console\Commands;

use Illuminate\Console\Command;

class PlatformModuleUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update monosoft modules from env';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $gitbase    = env('GITBASE');
        $modules    = explode(",", env('MODULES', ''));

        foreach ($modules as $module) {
            $this->call('module:pull', [
                'repository' => $gitbase . DIRECTORY_SEPARATOR . str($module)->before("|")->toString(),
                '--directory' => str($module)->after("|")->toString()
            ]);
        }
    }
}
