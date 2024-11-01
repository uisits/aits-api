<?php

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Response\StudentEnrollment\StudentEnrollment;

class AitsStudentEnrollment
{
    /**
     * @return StudentEnrollment
     *
     * @throws \Exception
     */
    public static function get(string $uin, string $term)
    {
        try {
            $response = Http::aits()
                ->get('/StudentEnrollment/1_0/'.$uin.'/'.$term);

            if (! $response->successful()) {
                throw new \Exception('Student Enrollment request failed! '.$response);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new \Exception('Student Enrollment not found!');
            }

            return StudentEnrollment::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new \Exception('Student Enrollment request failed! '.$exception->getMessage());
        }
    }
}
