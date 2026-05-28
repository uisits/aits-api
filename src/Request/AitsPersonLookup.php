<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Response\Person\Person;

class AitsPersonLookup
{
    /**
     * @throws \Exception
     */
    public static function get(string $uin): Data
    {
        try {
            $response = Http::aitsPerson()
                ->get('/PersonLookup/1_0/'.$uin);

            if (! $response->successful()) {
                throw new AitsRequestFailed('Person Lookup request failed! '.$response, 500);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new AitsRequestFailed('Person not found!', 404);
            }

            return Person::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Person Lookup request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
