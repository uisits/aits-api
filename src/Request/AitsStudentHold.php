<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Response\StudentHold\StudentHold;

class AitsStudentHold
{
    /**
     * @return StudentHold
     *
     * @throws \Exception
     */
    public static function get(string $uin): Data
    {
        try {
            $response = Http::aits()
                ->get('/StudentHolds/1_0/'.$uin);

            if (! $response->successful()) {
                throw new AitsRequestFailed('Student Holds request failed!', 500);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new AitsRequestFailed('Student Holds not found!', 404);
            }

            return StudentHold::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Student Holds request failed! '.$exception, $exception->getCode(), $exception);
        }
    }

    /**
     * @throws \Exception
     */
    public static function put(string $uin): Collection
    {
        try {
            $response = Http::aits()
                ->put('/StudentHolds/1_0/'.$uin);

            if (! $response->successful()) {
                throw new AitsRequestFailed('Student Hold put request failed!', 500);
            }

            return StudentHold::collect($response->collect('list'));
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Student Hold put request failed! '.$exception, $exception->getCode(), $exception);
        }
    }
}
