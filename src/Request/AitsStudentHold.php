<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ItemNotFoundException;
use Spatie\LaravelData\Data;
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
                throw new \Exception('Student Holds request failed!');
            }

            if ($response->collect('list')->isEmpty()) {
                throw new ItemNotFoundException('Student Holds not found!');
            }

            return StudentHold::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new \Exception('Student Holds request failed! '.$exception, $exception->getCode(), $exception);
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
                throw new \Exception('Student Hold put request failed!');
            }

            return StudentHold::collect($response->collect('list'));
        } catch (\Exception $exception) {
            throw new \Exception('Student Hold put request failed! '.$exception, $exception->getCode(), $exception);
        }
    }
}
