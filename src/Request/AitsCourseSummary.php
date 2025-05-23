<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Response\CourseSummary\CourseSummary;

class AitsCourseSummary
{
    /**
     * @return CourseSummary
     *
     * @throws \Exception
     */
    public static function get(string $term): \Spatie\LaravelData\DataCollection|\Spatie\LaravelData\PaginatedDataCollection|\Spatie\LaravelData\CursorPaginatedDataCollection|\Illuminate\Support\Enumerable|\Illuminate\Pagination\AbstractPaginator|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Pagination\AbstractCursorPaginator|\Illuminate\Contracts\Pagination\CursorPaginator|array
    {
        try {
            $response = Http::aits()
                ->get('/CourseSummary/1_0/'.$term);

            if (! $response->successful()) {
                throw new \Exception('Course Summary request failed! '.$response);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new \Exception('Course Summary not found!');
            }

            return CourseSummary::collect($response->collect('list'));
        } catch (\Exception $exception) {
            throw new \Exception('Course Summary request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
