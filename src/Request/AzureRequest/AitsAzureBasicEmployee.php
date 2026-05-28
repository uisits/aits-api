<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request\AzureRequest;

use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Response\AzureBasicEmployee\AzureEmployee;

class AitsAzureBasicEmployee
{
    /**
     * @return AzureEmployee
     *
     * @throws \Exception
     */
    public static function get(string $uin): Data
    {
        try {
            $response = Http::aitsAzure()
                ->get('/employee/basic-employee-query/'.$uin);

            if (! $response->successful()) {
                throw new AitsRequestFailed('Employee Query failed! '.$response, 500);
            }

            return AzureEmployee::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Employee Query failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
