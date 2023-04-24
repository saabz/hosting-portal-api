<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($message, $data) {
            return Response::json([
              'responseCode'  => 200,
              'responseMessage'  => $message,
              'responseData' => $data,
            ]);
        });
    
        Response::macro('error', function ($message, $status = 400) {
            return Response::json([
              'responseCode'  => $status,
              'responseMessage'  => $message,
              'responseData' => null,
            ], $status);
        });
    }
}
