<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use App\Services\AuthCookieService;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AuthCookieService::class);
    }
    
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Http::macro('ndeHttp', function($accessToken){
            if (config('nde.http_verify') === true){
                return Http::withHeaders(['Authorization' => 'Bearer ' . $accessToken]);
            }
            else {
                return Http::withoutVerifying()->withHeaders(['Authorization' => 'Bearer ' . $accessToken]);
            }
        });

        Http::macro('ndeHttpAuth', function(){
            if (config('nde.http_verify') === true){
                return Http::asForm();
            }
            else {
                return Http::withoutVerifying()->asForm();
            }
        });
    }
}
