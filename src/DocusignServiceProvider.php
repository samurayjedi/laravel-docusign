<?php
namespace Samurayjedi\LaravelDocusign;

use Illuminate\Support\ServiceProvider;

class DocusignServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $path = base_path('routes/docusign.php');
        if (file_exists($path)) {
            $this->loadRoutesFrom($path);
        }
        // $this->loadViewsFrom(__DIR__.'/../views', 'inspire');
    }
}