<?php

namespace Monoland\Platform\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Monoland\Platform\Services\GitModule;
use Symfony\Component\Process\Process;

class PlatformModuleDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:delete
        
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete selected module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
