<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Response\StudentRoster\StudentRoster;

class AitsStudentRoster
{
    /**
     * @throws \Exception
     */
    public static function get(string $term, string $crn): Collection
    {
        try {
            $response = Http::aits()
                ->get('/StudentRoster/1_0/'.$term.'/'.$crn);

            if (! $response->successful()) {
                throw new AitsRequestFailed('Student Roster request failed!', 500);
            }

            return StudentRoster::collect($response->collect('list'));
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Student Roster request failed!'.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
