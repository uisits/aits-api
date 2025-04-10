<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request\AzureRequest;

use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Response\AzureBasicEmployee\AzureEmployee;

class AitsAzureBasicEmployee
{
    /**
     * @return AzureEmployee
     *
     * @throws \Exception
     */
    public static function get(string $uin): \Spatie\LaravelData\Data
    {
        try {
            $response = Http::aitsAzure()
                ->get('/employee/basic-employee-query/'.$uin);

            if (! $response->successful()) {
                throw new \Exception('Employee Query failed! '.$response);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new \Exception('Employee not found!');
            }

            return AzureEmployee::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new \Exception('Employee Query failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
