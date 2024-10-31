<?php

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Response\BasicLearner\BasicLearner;

class AitsBasicLearner
{
    /**
     * @return BasicLearner
     *
     * @throws \Exception
     */
    public static function get(string $uin, string $term)
    {
        try {
            $response = Http::aits()
                ->get('/BasicLearner/1_0/'.$uin.'/'.$term);

            if (! $response->successful()) {
                throw new \Exception('Course Detail request failed! '.$response);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new \Exception('Course Detail not found!');
            }

            return BasicLearner::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new \Exception('Course Detail request failed! '.$exception->getMessage());
        }
    }
}
