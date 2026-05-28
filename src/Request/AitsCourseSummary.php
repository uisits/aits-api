<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\CursorPaginatedDataCollection;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;
use Uisits\AitsApi\Response\CourseSummary\CourseSummary;

class AitsCourseSummary
{
    /**
     * @return CourseSummary
     *
     * @throws \Exception
     */
    public static function get(string $term): DataCollection|PaginatedDataCollection|CursorPaginatedDataCollection|Enumerable|AbstractPaginator|Paginator|AbstractCursorPaginator|CursorPaginator|array
    {
        try {
            $response = Http::aits()
                ->get('/CourseSummary/1_0/'.$term);

            if (! $response->successful()) {
                throw new \Exception('Course Summary request failed! '.$response);
            }

            return CourseSummary::collect($response->collect('list'));
        } catch (\Exception $exception) {
            throw new \Exception('Course Summary request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
