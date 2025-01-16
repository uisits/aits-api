<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Response\Person\Person;

class AitsPersonLookup
{
    /**
     * @throws \Exception
     */
    public static function get(string $uin): \Spatie\LaravelData\Data
    {
        try {
            $response = Http::aitsPerson()
                ->get('/PersonLookup/1_0/'.$uin);

            if (! $response->successful()) {
                throw new \Exception('Person Lookup request failed! '.$response);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new \Exception('Person not found!');
            }

            return Person::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            dd($exception->getMessage());
            throw new \Exception('Person Lookup request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
