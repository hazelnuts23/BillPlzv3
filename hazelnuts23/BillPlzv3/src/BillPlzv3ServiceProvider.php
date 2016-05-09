<?php
namespace hazelnuts23\BillPlzv3;

use Illuminate\Support\ServiceProvider;

class BillPlzv3ServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // use this if your package has views
        $this->loadViewsFrom(realpath(__DIR__.'/resources/views'), 'BillPlzv3');

        // use this if your package has lang files
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'BillPlzv3');

    }

    public function register()
    {
        $this->registerBillPlz();

    }
    private function registerBillPlz()
    {
        $this->app->bind('BillPlzv3',function($app){
            return new BillPlz($app);
        });
    }
}