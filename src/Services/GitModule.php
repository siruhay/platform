<?php

namespace Monoland\Platform\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GitModule
{
    /**
     * Github::open(directory)
     * ->reset()    : git reset --hard && git clean -df && git switch main
     * ->clone(repo, directory)
     * ->fetch()    : git fetch --all --tags --prune --prune-tags
     * ->update()   : git pull origin main => on dev | git checkout $(git describe --tags $(git rev-list --tags --max-count=1)) => on production
     * ->info()     : git log --name-status HEAD^..HEAD => on dev | git tag -n => on production
     */

    /**
     * fetch function
     *
     * @param [type] $module
     * @return void
     */
    public static function fetch($module = null): void
    {
        $directory = base_path('modules' . DIRECTORY_SEPARATOR . ($module ?: 'system'));

        $process = Process::fromShellCommandline('git fetch --all --tags --prune --prune-tags');
        $process->setWorkingDirectory($directory);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    /**
     * remoteCommitLast function
     *
     * @param [type] $module
     * @return string
     */
    public static function remoteCommitLast($module = null): string
    {
        $directory = base_path('modules' . DIRECTORY_SEPARATOR . ($module ?: 'system'));

        $process = Process::fromShellCommandline('git rev-parse `git branch -r --sort=committerdate | tail -1`');
        $process->setWorkingDirectory($directory);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return trim(preg_replace('/\s+/', ' ', $process->getOutput()));
    }

    /**
     * localCommitLast function
     *
     * @param [type] $module
     * @return string
     */
    public static function localCommitLast($module = null): string
    {
        $directory = base_path('modules' . DIRECTORY_SEPARATOR . ($module ?: 'system'));

        $process = Process::fromShellCommandline('git rev-parse --verify HEAD 2> /dev/null');
        $process->setWorkingDirectory($directory);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return trim(preg_replace('/\s+/', ' ', $process->getOutput()));
    }

    /**
     * remoteTagLast function
     *
     * @param [type] $module
     * @return string
     */
    public static function remoteTagLast($module = null): string
    {
        $directory = base_path('modules' . DIRECTORY_SEPARATOR . ($module ?: 'system'));

        $process = Process::fromShellCommandline('git tag | tail -1');
        $process->setWorkingDirectory($directory);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return trim(preg_replace('/\s+/', ' ', $process->getOutput()));
    }

    /**
     * localTagLast function
     *
     * @param [type] $module
     * @return string
     */
    public static function localTagLast($module = null): string
    {
        $directory = base_path('modules' . DIRECTORY_SEPARATOR . ($module ?: 'system'));

        $process = Process::fromShellCommandline('git describe --exact-match --tags 2> /dev/null');
        $process->setWorkingDirectory($directory);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return trim(preg_replace('/\s+/', ' ', $process->getOutput()));
    }
}
