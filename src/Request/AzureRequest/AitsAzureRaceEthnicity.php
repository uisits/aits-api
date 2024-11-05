<?php

namespace Uisits\AitsApi\Request\AzureRequest;

use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Response\RaceEthnicity\RaceEthnicity;

class AitsAzureRaceEthnicity
{
    /**
     * @return RaceEthnicity
     *
     * @throws \Exception
     */
    public static function get(string $uin)
    {
        try {
            $response = Http::aitsAzure()
                ->get('/person/race-ethnicity-query/'.$uin);

            if (! $response->successful()) {
                throw new \Exception('Race Ethnicity request failed! '.$response);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new \Exception('Race Ethnicity not found!');
            }

            return RaceEthnicity::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new \Exception('Race Ethnicity request failed! '.$exception->getMessage());
        }
    }
}
