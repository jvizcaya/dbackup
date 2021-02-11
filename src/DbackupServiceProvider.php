<?php

namespace Jvizcaya\Dbackup;

use Illuminate\Support\ServiceProvider;
use Jvizcaya\Dbackup\Commands\DbackupGenerate;

class DbackupServiceProvider extends ServiceProvider
{
      /**
       * Register any application services.
       *
       * @return void
       */
      public function register()
      {
          // Register dbackup config file
          $this->mergeConfigFrom(
            __DIR__.'/../config/dbackup.php', 'dbackup'
          );
      }

      /**
       * Bootstrap any application services.
       *
       * @return void
       */
      public function boot()
      {
            // Load dbackup config file
            $this->publishes([
                __DIR__.'/../config/dbackup.php' => config_path('dbackup.php'),
            ]);

            // Load dbackup commands
            if ($this->app->runningInConsole()) {
                $this->commands([
                  DbackupGenerate::class
                ]);
            }
      }
}
