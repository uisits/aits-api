<?php

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Response\StudentRoster\StudentRoster;

class AitsStudentRoster
{
    /**
     * @return array|\Illuminate\Contracts\Pagination\CursorPaginator|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Pagination\AbstractCursorPaginator|\Illuminate\Pagination\AbstractPaginator|\Illuminate\Support\Collection|\Illuminate\Support\Enumerable|\Illuminate\Support\LazyCollection|\Spatie\LaravelData\CursorPaginatedDataCollection|\Spatie\LaravelData\DataCollection|\Spatie\LaravelData\PaginatedDataCollection
     *
     * @throws \Exception
     */
    public static function get(string $term, string $crn): \Illuminate\Support\Collection
    {
        try {
            $response = Http::aits()
                ->get('/StudentRoster/1_0/'.$term.'/'.$crn);

            if (! $response->successful()) {
                throw new \Exception('Course Detail request failed!');
            }

            return StudentRoster::collect($response->collect('list'));
        } catch (\Exception $exception) {
            throw new \Exception('Course Detail request failed!'.$exception->getMessage());
        }
    }
}
