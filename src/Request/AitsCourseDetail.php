<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Response\CourseDetail\CourseDetail;

class AitsCourseDetail
{
    /**
     * @return Collection<int, CourseDetail>
     *
     * @throws \Exception
     */
    public static function get(string $term, string $crn): Collection
    {
        try {
            $response = Http::aits()
                ->get('/CourseDetail/1_0/'.$term.'/'.$crn);

            if (! $response->successful()) {
                throw new AitsRequestFailed('Course Detail request failed! '.$response, 500);
            }

            return CourseDetail::collect($response->collect('list'));
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Course Detail request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
