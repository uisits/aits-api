<?php

declare(strict_types=1);

namespace Uisits\AitsApi;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AitsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/aits-api.php', 'aits-api',
        );

        // publish config
        $this->publishes([
            __DIR__.'/../config/aits-api.php' => config_path('aits-api.php'),
        ], 'aits-api');
    }

    public function register(): void
    {
        /**
         * Banner api endpoint.
         */
        Http::macro('aits', function () {
            return Http::baseUrl(config('aits-api.base_url'))
                ->when(config('aits-api.with_proxy'), fn ($request) => $this->withProxy($request));
        });

        /**
         * Http macro for AITS-Person web services
         */
        Http::macro('aitsPerson', function () {
            return Http::baseUrl(config('aits-api.person_base_url'))
                ->when(config('aits-api.with_proxy'), fn ($request) => $this->withProxy($request));
        });

        /**
         * Http macro for AITS Azure apis
         */
        Http::macro('aitsAzure', function () {
            return Http::baseUrl(config('aits-api.azure.base_url'))
                ->withHeaders([
                    'Cache-Control' => 'no-cache',
                    'Ocp-Apim-Subscription-Key' => config('aits-api.azure.portal_key'),
                ])
                ->when(config('aits-api.with_proxy'), fn ($request) => $this->withProxy($request));
        });
    }

    private function withProxy($request): void
    {
        $proxy = $this->proxyUrl();

        if ($proxy === null) {
            return;
        }

        $request->withOptions([
            'proxy' => $proxy,
        ]);
    }

    private function proxyUrl(): ?string
    {
        $host = config('aits-api.proxy.host');
        $port = config('aits-api.proxy.port');

        if (blank($host) || blank($port)) {
            return null;
        }

        $username = config('aits-api.proxy.username');
        $password = config('aits-api.proxy.password');
        $credentials = filled($username)
            ? $username.(filled($password) ? ':'.$password : '').'@'
            : '';

        return config('aits-api.proxy.scheme').$credentials.$host.':'.$port;
    }
}
