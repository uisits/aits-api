<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Response\StudentAdvisor\StudentAdvisor;

class AitsStudentAdvisor
{
    /**
     * @return StudentAdvisor
     *
     * @throws \Exception
     */
    public static function get(string $uin, string $term): Data
    {
        try {
            $response = Http::aits()
                ->get('/StudentAdvisors/1_0/'.$uin.'/'.$term);

            if (! $response->successful()) {
                throw new AitsRequestFailed('Student Advisor request failed!', 500);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new AitsRequestFailed('Student Advisor not found!', 404);
            }

            return StudentAdvisor::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Student Advisor request failed!', $exception->getCode(), $exception);
        }
    }
}
