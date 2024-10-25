<?php

namespace Siruhay\Platform\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class PlatformMakeModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make { module }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make monosoft platform new module';

    /**
     * The fileSystem variable
     *
     * @var [type]
     */
    protected $fileSystem;

    /**
     * The authorName variable
     *
     * @var string
     */
    protected $authorName = 'monoland';

    /**
     * The authorEmail variable
     *
     * @var string
     */
    protected $authorEmail = 'monoland.soft@gmail.com';

    /**
     * The authorVendor variable
     *
     * @var string
     */
    protected $authorVendor = 'monosoft';

    /**
     * The defaultConnection variable
     *
     * @var string
     */
    protected $defaultConnection = 'platform';

    /**
     * The moduleName variable
     *
     * @var [type]
     */
    protected $moduleName;

    /**
     * The modulePath variable
     *
     * @var [type]
     */
    protected $modulePath;

    /**
     * The namespace variable
     *
     * @var string
     */
    protected $namespace = 'Module';

    /**
     * The module folder structure
     *
     * @var array
     */
    protected $folders = [
        'database/masters',
        'database/migrations',
        'database/seeders',
        'frontend/pages/dashboard',
        'frontend/router',
        'resources/views',
        'resources/views/reports',
        'routes',
        'src/Http/Controllers',
        'src/Http/Resources',
        'src/Imports',
        'src/Models',
        'src/Policies',
        'src/Providers',
    ];

    /**
     * The module files in folder
     *
     * @var array
     */
    protected $files = [
        'database/gitignore.stub' => 'database/.gitignore',
        'database/masters/gitkeep.stub' => 'database/masters/.gitkeep',
        'database/migrations/gitkeep.stub' => 'database/migrations/.gitkeep',
        'database/seeders/database-seeder.stub' => 'database/seeders/DatabaseSeeder.php',
        'database/seeders/base-seeder.stub' => 'database/seeders/$MODULE$BaseSeeder.php',
        'database/seeders/data-seeder.stub' => 'database/seeders/$MODULE$DataSeeder.php',
        'database/seeders/user-seeder.stub' => 'database/seeders/$MODULE$UserSeeder.php',
        'frontend/pages/base.stub' => 'frontend/pages/Base.vue',
        'frontend/pages/dashboard/index.stub' => 'frontend/pages/dashboard/index.vue',
        'frontend/router/index.stub' => 'frontend/router/index.js',
        'resources/views/welcome.stub' => 'resources/views/welcome.blade.php',
        'resources/views/reports/css.stub' => 'resources/views/reports/css.blade.php',
        'routes/api.stub' => 'routes/api.php',
        'src/Http/Controllers/dashboard.stub' => 'src/Http/Controllers/DashboardController.php',
        'src/Http/Resources/gitkeep.stub' => 'src/Http/Resources/.gitkeep',
        'src/Imports/dataimport.stub' => 'src/Imports/DataImport.php',
        'src/Models/gitkeep.stub' => 'src/Models/.gitkeep',
        'src/Policies/gitkeep.stub' => 'src/Policies/.gitkeep',
        'src/Providers/service.stub' => 'src/Providers/$MODULE$ServiceProvider.php',
        'src/Providers/event.stub' => 'src/Providers/EventServiceProvider.php',
        'src/Providers/route.stub' => 'src/Providers/RouteServiceProvider.php',
        'composer.stub' => 'composer.json',
        'module.stub' => 'module.json',
        'gitattributes.stub' => '.gitattributes',
        'gitignore.stub' => '.gitignore'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->fileSystem = $this->laravel['files'];

        $this->moduleName = $this->argument('module');

        $this->modulePath = base_path(
            'modules' .
                DIRECTORY_SEPARATOR .
                str($this->moduleName)->lower()->toString()
        );

        /** GENERATE FOLDER */
        $this->generateFolders();

        /** GENERATE FILES */
        $this->generateFiles();

        /** PUT ON CACHE */
        if (Cache::has('modules')) {
            Cache::forget('modules');
        }

        Cache::flexible('modules', [60, 3600], function () {
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

    /**
     * generateFolders function
     *
     * @return void
     */
    protected function generateFolders(): void
    {
        /** CHECKFOLDER IF EXISTS */
        if ($this->fileSystem->exists($this->modulePath)) {
            return;
        }

        $this->fileSystem->makeDirectory(
            $this->modulePath,
            0755,
            true
        );

        foreach ($this->folders as $folder) {
            $this->fileSystem->makeDirectory(
                $this->modulePath . DIRECTORY_SEPARATOR . $folder,
                0755,
                true
            );
        }
    }

    /**
     * generateFiles function
     *
     * @return void
     */
    protected function generateFiles(): void
    {
        foreach ($this->files as $stub => $file) {
            if (str($file)->contains('$MODULE$')) {
                $file = str_replace('$MODULE$', $this->moduleName, $file);
            }

            $filepath = $this->modulePath . DIRECTORY_SEPARATOR . $file;

            if ($this->fileSystem->exists($filepath)) {
                continue;
            }

            $this->fileSystem->put(
                $filepath,
                $this->getStubContents($stub)
            );
        }
    }

    /**
     * Get the contents of the specified stub file by given stub name.
     *
     * @param $stub
     *
     * @return string
     */
    protected function getStubContents($stub): string
    {
        $stub = $this->fileSystem->get(
            __DIR__ . DIRECTORY_SEPARATOR . 'module-stubs' . DIRECTORY_SEPARATOR . $stub
        );

        $searches = [
            '$AUTHOR_NAME$',
            '$AUTHOR_EMAIL$',
            '$AUTHOR_VENDOR$',
            '$CONNECTION$',
            '$NAMESPACE$',
            '$NAMESPACE_LOWER$',
            '$MODULE$',
            '$MODULE_LOWER$',
            '$MODULE_SLUG$',
        ];

        $replacements = [
            $this->authorName,
            $this->authorEmail,
            $this->authorVendor,
            $this->defaultConnection,
            str($this->namespace)->studly()->toString(),
            str($this->namespace)->lower()->toString(),
            str($this->moduleName)->studly()->toString(),
            str($this->moduleName)->lower()->toString(),
            str($this->namespace . '-' . $this->moduleName)->slug()->toString(),
        ];

        return str_replace(
            $searches,
            $replacements,
            $stub
        );
    }
}
