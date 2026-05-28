<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Response\StudentEnrollment\StudentEnrollment;

class AitsStudentEnrollment
{
    /**
     * @return StudentEnrollment
     *
     * @throws \Exception
     */
    public static function get(string $uin, string $term): Data
    {
        try {
            $response = Http::aits()
                ->get('/StudentEnrollment/1_0/'.$uin.'/'.$term);

            if (! $response->successful()) {
                throw new AitsRequestFailed('Student Enrollment request failed! '.$response, 500);
            }

            return StudentEnrollment::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Student Enrollment request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
