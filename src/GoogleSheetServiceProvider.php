<?php

namespace laravelstudio\laravelgooglesheetintegration;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


class GoogleSheetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    
    public function register()
    {
        $this->app->bind('laravelstudio\laravelgooglesheetintegration\contracts\userGoogleSheetInterface', 'laravelstudio\laravelgooglesheetintegration\repositories\userGoogleSheetRepository');
        $this->app->bind('laravelstudio\laravelgooglesheetintegration\contracts\UserInterface', 'laravelstudio\laravelgooglesheetintegration\repositories\UserRepository');
        $this->app->bind('laravelstudio\laravelgooglesheetintegration\contracts\googleSheetInterface', 'laravelstudio\laravelgooglesheetintegration\repositories\googleSheetUpdateRepository');               
        $this->app->bind('googleSheet',function(){
            return $this->app->make('laravelstudio\laravelgooglesheetintegration\GoogleSheetController');
        });      


    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {        
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'laravelgooglesheetintegration');
        $this->publishes([
            __DIR__.'/migrations' => database_path('migrations')
        ]);
        $this->publishes([            
            __DIR__.'/views/layout.blade.php' => resource_path('views/vendor/laravelgooglesheetintegration/layout.blade.php'),
            __DIR__.'/views/signin.blade.php' => resource_path('views/vendor/laravelgooglesheetintegration/signin.blade.php'),            
        ]);
        $this->publishes([
            __DIR__.'/config/googlesheet.php' => config_path('googlesheet.php'),
        ]);
        $this->publishes([
            __DIR__.'/assets' => public_path('vendor/laravelgooglesheetintegration'),
        ], 'public');
        
    }
}
