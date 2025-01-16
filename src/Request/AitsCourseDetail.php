<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Response\CourseDetail\CourseDetail;

class AitsCourseDetail
{
    /**
     * @return CourseDetail
     *
     * @throws \Exception
     */
    public static function get(string $term, string $crn): \Spatie\LaravelData\Data
    {
        try {
            $response = Http::aits()
                ->get('/CourseDetail/1_0/'.$term.'/'.$crn);

            if (! $response->successful()) {
                throw new \Exception('Course Detail request failed! '.$response);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new \Exception('Course Detail not found!');
            }

            return CourseDetail::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new \Exception('Course Detail request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
