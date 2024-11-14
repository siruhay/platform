<?php

namespace Monoland\Platform\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class PlatformModuleClone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:clone
        {repository}
        {--directory=}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone module from git-source';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = $this->option('directory') ?: File::basename($this->argument('repository'));

        if (File::isDirectory(base_path('modules' . DIRECTORY_SEPARATOR . $directory))) {
            $this->info('module already exists');
            return;
        }

        $process = new Process([
            'git',
            'clone',
            $this->argument('repository'),
            $this->option('directory')
        ]);

        $process->setWorkingDirectory(base_path() . DIRECTORY_SEPARATOR . 'modules');
        $process->run();
    }
}
