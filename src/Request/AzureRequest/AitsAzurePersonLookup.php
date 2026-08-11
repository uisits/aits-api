<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request\AzureRequest;

use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Response\AzurePerson\AzurePerson;

class AitsAzurePersonLookup
{
    /**
     * @return AzurePerson
     *
     * @throws \Exception
     */
    public static function get(string $uin)
    {
        try {
            $response = Http::aitsAzure()
                ->get('/person/person-data-query/'.$uin);

            if (! $response->successful()) {
                throw new AitsRequestFailed('Person request failed! '.$response, 500);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new AitsRequestFailed('Person not found!', 404);
            }

            return AzurePerson::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Person request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
