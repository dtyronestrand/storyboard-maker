<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Google\Client;

class GoogleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
   $this->app->singleton(Client::class, function($app){
    $client = new Client();
    $client->setClientId(config('services.google.client_id'));
    $client->setClientSecret(config('services.google.client_secret'));
    $client->setRedirectUri(config('services.google.redirect_uri'));
    $client->setScopes(['https://www.googleapis.com/auth/documents']);


   
    
    return $client;
   });
}
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
