<?php

declare(strict_types=1);

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\LaravelData\LaravelDataServiceProvider;
use Uisits\AitsApi\AitsServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelDataServiceProvider::class,
            AitsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('aits-api.base_url', 'https://aits.test/student');
        $app['config']->set('aits-api.person_base_url', 'https://aits.test/person');
        $app['config']->set('aits-api.azure.base_url', 'https://aits.test/azure');
        $app['config']->set('aits-api.azure.portal_key', 'test-key');
        $app['config']->set('aits-api.with_proxy', false);
    }
}
