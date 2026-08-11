<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request\AzureRequest;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Response\CourseDetail\CourseDetail;

class AitsAzureCourseDetail
{
    public static function get(string $term, string $crn): Collection
    {
        try {
            $response = Http::aitsAzure()
                ->get('/student-course/course-detail-query/'.$term.'/'.$crn);

            if (! $response->successful()) {
                throw new AitsRequestFailed('Course Detail request failed! '.$response, 500);
            }

            return CourseDetail::collect($response->collect('list'));
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Course Detail request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
