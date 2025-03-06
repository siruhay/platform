<?php

namespace Monoland\Platform\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class PlatformInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install monoland platform';

    /**
     * handle function
     *
     * @return void
     */
    public function handle()
    {
        $this->patchComposerJSON();
        $this->patchEnvirontment();
        $this->addStatefulApi();
        $this->removeFileAndFolder();

        $this->call('vendor:publish', [
            '--tag' => 'siruhay-config',
            '--force' => true
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'siruhay-assets',
            '--force' => true
        ]);

        $this->runComposerUpdate();
    }

    /**
     * removeFileAndFolder function
     *
     * @return void
     */
    protected function removeFileAndFolder(): void
    {
        if (File::isDirectory(resource_path('js'))) {
            File::deleteDirectory(resource_path('js'));
        }

        if (File::isDirectory(resource_path('css'))) {
            File::deleteDirectory(resource_path('css'));
        }

        if (File::isDirectory(resource_path('src'))) {
            File::deleteDirectory(resource_path('src'));
        }

        if (File::exists(base_path('vite.config.js'))) {
            File::delete(base_path('vite.config.js'));
        }

        if (File::isDirectory(app_path('Models'))) {
            File::deleteDirectory(app_path('Models'));
        }

        if (File::isDirectory(resource_path('views' . DIRECTORY_SEPARATOR . 'welcome.blade.php'))) {
            File::deleteDirectory(resource_path('views' . DIRECTORY_SEPARATOR . 'welcome.blade.php'));
        }

        if (File::isDirectory(database_path('migrations'))) {
            File::deleteDirectory(database_path('migrations'));

            File::makeDirectory(database_path('migrations'));
        }
    }

    /**
     * addStatefulApi function
     *
     * @return void
     */
    protected function addStatefulApi(): void
    {
        $appFile = base_path('bootstrap' . DIRECTORY_SEPARATOR . 'app.php');
        $content = file_get_contents($appFile);

        if (str_contains($content, 'api: __DIR__.\'/../routes/api.php\',')) {
            return;
        }

        $this->call('install:api', [
            '--without-migration-prompt' => true
        ]);

        if (str_contains($content, '$middleware->statefulApi();')) {
            return;
        }

        if (str_contains($content, '->withMiddleware(function (Middleware $middleware) {')) {
            (new Filesystem())->replaceInFile(
                '->withMiddleware(function (Middleware $middleware) {',
                '->withMiddleware(function (Middleware $middleware) {' . PHP_EOL . "\t\t" . '$middleware->statefulApi();',
                $appFile,
            );
        }
    }

    /**
     * patchComposerJSON function
     *
     * @return void
     */
    protected function patchComposerJSON(): void
    {
        $composerFile = base_path('composer.json');

        $content = json_decode(file_get_contents($composerFile), true);

        if (array_key_exists('repositories', $content)) {
            $content['repositories'] = [
                ['type' => 'path', 'url' => 'packages/monoland/starterkit'],
                ['type' => 'path', 'url' => 'modules/*']
            ];
        }

        if (array_key_exists('extra', $content) && !array_key_exists('merge-plugin', $content['extra'])) {
            $content['extra']['merge-plugin'] = [
                'include' => [
                    "modules/*/composer.json"
                ]
            ];
        }

        $content = json_encode($content, JSON_PRETTY_PRINT);
        $content = str_replace('\/', '/', $content);

        file_put_contents($composerFile, $content);
    }

    /**
     * patchEnvirontment function
     *
     * @return void
     */
    protected function patchEnvirontment(): void
    {
        $envFile = base_path('.env');
        $content = file_get_contents($envFile);

        if (str_contains($content, 'APP_TIMEZONE=UTC')) {
            (new Filesystem())->replaceInFile(
                'APP_TIMEZONE=UTC',
                'APP_TIMEZONE="Asia/Jakarta"',
                $envFile,
            );
        }

        if (str_contains($content, 'APP_LOCALE=en')) {
            (new Filesystem())->replaceInFile(
                'APP_LOCALE=en',
                'APP_LOCALE=id',
                $envFile,
            );
        }

        if (str_contains($content, 'APP_FAKER_LOCALE=en_US')) {
            (new Filesystem())->replaceInFile(
                'APP_FAKER_LOCALE=en_US',
                'APP_FAKER_LOCALE=id_ID',
                $envFile,
            );
        }

        if (str_contains($content, 'DB_CONNECTION=pgsql')) {
            (new Filesystem())->replaceInFile(
                'DB_CONNECTION=pgsql',
                'DB_CONNECTION=platform',
                $envFile,
            );
        }

        if (str_contains($content, 'SESSION_DOMAIN=null')) {
            (new Filesystem())->replaceInFile(
                'SESSION_DOMAIN=null',
                'SESSION_DOMAIN=.siruhay.devapp.test',
                $envFile,
            );
        }

        if (str_contains($content, 'VITE_APP_NAME="${APP_NAME}"') && !str_contains($content, 'AUTH_MODEL=Module\System\Models\SystemUser')) {
            (new Filesystem())->replaceInFile(
                'VITE_APP_NAME="${APP_NAME}"',
                'VITE_APP_NAME="${APP_NAME}"' . PHP_EOL .
                    'AUTH_MODEL=Module\System\Models\SystemUser' . PHP_EOL .
                    'AUTH_PASSWORD_RESET_TOKEN_TABLE=system_password_reset_tokens' . PHP_EOL .
                    'DB_CACHE_TABLE=system_cache' . PHP_EOL .
                    'DB_QUEUE_TABLE=system_jobs' . PHP_EOL .
                    'DB_QUEUE_BATCH_TABLE=system_job_batches' . PHP_EOL .
                    'DB_QUEUE_FAILED_TABLE=system_failded_jobs' . PHP_EOL .
                    'SESSION_TABLE=system_sessions',
                $envFile,
            );
        }
    }

    /**
     * runComposerUpdate function
     *
     * @return void
     */
    protected function runComposerUpdate(): void
    {
        $process = new Process([
            'composer',
            'update'
        ]);

        $process->setWorkingDirectory(base_path());
        $process->run();
    }
}
