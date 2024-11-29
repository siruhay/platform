<?php

namespace Monoland\Platform;

use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Monoland\Platform\Http\Middleware\Impersonate;
use Monoland\Platform\Console\Commands\PlatformInstall;
use Monoland\Platform\Console\Commands\PlatformMakeJob;
use Monoland\Platform\Console\Commands\PlatformMakeSeed;
use Monoland\Platform\Console\Commands\PlatformMakeEvent;
use Monoland\Platform\Console\Commands\PlatformMakeModel;
use Monoland\Platform\Console\Commands\PlatformMakeExport;
use Monoland\Platform\Console\Commands\PlatformMakeImport;
use Monoland\Platform\Console\Commands\PlatformMakeModule;
use Monoland\Platform\Console\Commands\PlatformMakePolicy;
use Monoland\Platform\Console\Commands\PlatformModuleList;
use Monoland\Platform\Console\Commands\PlatformModulePull;
use Monoland\Platform\Console\Commands\PlatformModuleSeed;
use Monoland\Platform\Console\Commands\PlatformMakeCommand;
use Monoland\Platform\Console\Commands\PlatformMakeReplica;
use Monoland\Platform\Console\Commands\PlatformModuleClone;
use Monoland\Platform\Console\Commands\PlatformMakeFrontend;
use Monoland\Platform\Console\Commands\PlatformMakeListener;
use Monoland\Platform\Console\Commands\PlatformMakeResource;
use Monoland\Platform\Console\Commands\PlatformModuleDelete;
use Monoland\Platform\Console\Commands\PlatformModuleUpdate;
use Monoland\Platform\Console\Commands\PlatformMakeMigration;
use Monoland\Platform\Console\Commands\PlatformModuleInstall;
use Monoland\Platform\Console\Commands\PlatformModuleMigrate;
use Monoland\Platform\Console\Commands\PlatformMakeController;
use Monoland\Platform\Console\Commands\PlatformMakeNotification;

class ModularServiceProvider extends ServiceProvider
{
    /**
     * boot function
     *
     * @return void
     */
    public function boot(): void
    {
        /** ADD MIDDLEWARE */
        $this->app->router->pushMiddlewareToGroup('api', Impersonate::class);

        /** Disable wrapping of the outer-most resource array. */
        JsonResource::withoutWrapping();

        /** Prevent model relationships from being lazy loaded. */
        Model::preventLazyLoading();

        /** Prevent non-fillable attributes from being silently discarded. */
        Model::preventSilentlyDiscardingAttributes();

        /** Register Artisan Commands */
        $this->registerArtisanCommands();

        /** Boot and Register Modules */
        $this->bootAndRegisterModules();

        /** Publish asset, config and frontend-components */
        $this->publishes([
            __DIR__ . '/../.eslintrc.js' => base_path('.eslintrc.js'),
            __DIR__ . '/../config/database.php' => config_path('database.php'),
            __DIR__ . '/../config/cors.php' => config_path('cors.php'),
            __DIR__ . '/../modules' => base_path('modules'),
            __DIR__ . '/../routes' => base_path('routes'),
            __DIR__ . '/../seeders' => database_path('seeders'),
            __DIR__ . '/../vite.config.mjs' => base_path('vite.config.mjs'),
        ], 'siruhay-config');

        $this->publishes([
            __DIR__ . '/../frontend' => resource_path(),
            __DIR__ . '/../package.json' => base_path('package.json'),
        ], 'siruhay-frontend');

        $this->publishes([
            __DIR__ . '/../assets' => resource_path('assets'),
            __DIR__ . '/../avatars' => resource_path('avatars'),
            __DIR__ . '/../pdfjs' => resource_path('pdfjs'),
        ], 'siruhay-assets');
    }

    /**
     * register function
     *
     * @return void
     */
    public function register(): void
    {
        URL::forceScheme('https');

        Fortify::ignoreRoutes();
    }

    /**
     * registerArtisanCommands function
     *
     * @return void
     */
    protected function registerArtisanCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PlatformInstall::class,
                PlatformMakeCommand::class,
                PlatformMakeController::class,
                PlatformMakeEvent::class,
                PlatformMakeExport::class,
                PlatformMakeFrontend::class,
                PlatformMakeImport::class,
                PlatformMakeJob::class,
                PlatformMakeListener::class,
                PlatformMakeMigration::class,
                PlatformMakeModel::class,
                PlatformMakeModule::class,
                PlatformMakeNotification::class,
                PlatformMakePolicy::class,
                PlatformMakeReplica::class,
                PlatformMakeResource::class,
                PlatformMakeSeed::class,
                PlatformModuleClone::class,
                PlatformModuleDelete::class,
                PlatformModuleInstall::class,
                PlatformModuleList::class,
                PlatformModuleMigrate::class,
                PlatformModulePull::class,
                PlatformModuleSeed::class,
                PlatformModuleUpdate::class
            ]);
        }
    }

    /**
     * bootAndRegisterModules function
     *
     * @return void
     */
    protected function bootAndRegisterModules(): void
    {
        $modules = Cache::has('modules') && count(Cache::get('modules')) > 0 ?
            Cache::get('modules') :
            $this->scanModulesFolder();

        foreach ($modules as $module) {
            if (!File::exists(base_path('modules' . DIRECTORY_SEPARATOR . str($module->name)->lower()))) {
                continue;
            }

            if ($module->providers && is_array($module->providers)) {
                foreach ($module->providers as $provider) {
                    if (class_exists($provider)) {
                        with(new $provider($this->app))->boot();
                        with(new $provider($this->app))->register();
                    }
                }
            } else {
                if (class_exists($module->providers)) {
                    with(new $module->providers($this->app))->boot();
                    with(new $module->providers($this->app))->register();
                }
            }
        }
    }

    /**
     * scanModulesFolder function
     *
     * @return array
     */
    protected function scanModulesFolder(): array
    {
        Cache::forget('modules');

        return Cache::flexible('modules', [60, 3600], function () {
            $modules = [];

            /** Scan All-Module Except System */
            $folders = glob(base_path('modules') . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);

            foreach ($folders as $folder) {
                $json_path = $folder . DIRECTORY_SEPARATOR . 'module.json';

                if (!File::exists($json_path)) {
                    continue;
                }

                $content                = file_get_contents($json_path);
                $json_data              = json_decode($content, true);
                $module_name            = $json_data['name'];
                $json_data['directory'] = $folder;
                $modules[$module_name]  = $json_data;
            }

            if (count($modules) === 0) {
                return $modules;
            }

            /** Sort data by priority */
            array_multisort(array_column($modules, 'priority'), SORT_ASC, $modules);

            /** Convert array to object */
            foreach ($modules as $key => $module) {
                $modules[$key] = json_decode(json_encode($module), false);
            }

            return $modules;
        });
    }
}
