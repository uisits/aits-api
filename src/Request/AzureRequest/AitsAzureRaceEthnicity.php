<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request\AzureRequest;

use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Response\RaceEthnicity\RaceEthnicity;

class AitsAzureRaceEthnicity
{
    /**
     * @return RaceEthnicity
     *
     * @throws \Exception
     */
    public static function get(string $uin): Data
    {
        try {
            $response = Http::aitsAzure()
                ->get('/person/race-ethnicity-query/'.$uin);

            if (! $response->successful()) {
                throw new AitsRequestFailed('Race Ethnicity request failed! '.$response, 500);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new AitsRequestFailed('Race Ethnicity not found!', 404);
            }

            return RaceEthnicity::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Race Ethnicity request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
