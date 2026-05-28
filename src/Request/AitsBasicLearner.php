<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Response\BasicLearner\BasicLearner;

class AitsBasicLearner
{
    /**
     * @throws \Exception
     */
    public static function get(string $uin, string $term): Data
    {
        try {
            $response = Http::aits()
                ->get('/BasicLearner/1_0/'.$uin.'/'.$term);

            if (! $response->successful()) {
                throw new AitsRequestFailed('Basic Learner request failed! '.$response, 500);
            }

            return BasicLearner::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new \Exception('Basic Learner request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
