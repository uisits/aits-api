<?php

namespace Uisits\AitsApi;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AitsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/aits-api.php', 'aits-api',
        );

        // publish config
        $this->publishes([
            __DIR__.'/../config/aits-api.php' => config_path('aits-api.php'),
        ], 'aits-api');
    }

    public function register()
    {
        /**
         * Banner api endpoint.
         */
        Http::macro('aits', function () {
            return Http::baseUrl(config('aits-api.base_url'))
                ->when(config('aits-api.with_proxy'), function ($request) {
                    $request->withOptions([
                        'proxy' => str(config('aits-api.proxy.scheme'))
                            ->append(config('aits-api.proxy.username'))
                            ->append(':')
                            ->append(config('aits-api.proxy.password'))
                            ->append('@')
                            ->append(config('aits-api.proxy.host'))
                            ->append(':')
                            ->append(config('aits-api.proxy.port')),
                    ]);
                });
        });

        Http::macro('aitsPerson', function () {
            return Http::baseUrl(config('aits-api.person_base_url'))
                ->when(config('aits-api.with_proxy'), function ($request) {
                    $request->withOptions([
                        'proxy' => str(config('aits-api.proxy.scheme'))
                            ->append(config('aits-api.proxy.username'))
                            ->append(':')
                            ->append(config('aits-api.proxy.password'))
                            ->append('@')
                            ->append(config('aits-api.proxy.host'))
                            ->append(':')
                            ->append(config('aits-api.proxy.port')),
                    ]);
                });
        });
    }
}
