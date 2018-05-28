<?php

namespace Bluebik\DbPasswordSecure;

use Illuminate\Support\ServiceProvider;
use Bluebik\DbPasswordSecure\Console\Commands\SetupDatabase;

class DbPasswordSecureServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->registerHelpers();
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupDatabase::class,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {

    }

    /**
     * Register helpers file
     */
    protected function registerHelpers()
    {
        // Load the helpers in app/Http/helpers.php
        if (file_exists($file = app_path('helpers.php'))) {
            require $file;
        }
    }
}
