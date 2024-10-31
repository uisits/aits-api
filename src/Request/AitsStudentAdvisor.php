<?php

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ItemNotFoundException;
use Uisits\AitsApi\Response\StudentAdvisor\StudentAdvisor;

class AitsStudentAdvisor
{
    /**
     * @return StudentAdvisor
     *
     * @throws \Exception
     */
    public static function get(string $uin, string $term)
    {
        try {
            $response = Http::aits()
                ->get('/StudentAdvisors/1_0/'.$uin.'/'.$term);

            if (! $response->successful()) {
                throw new \Exception('Student Advisor request failed!');
            }

            if ($response->collect('list')->isEmpty()) {
                throw new ItemNotFoundException('Student Advisor information not found!');
            }

            return StudentAdvisor::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            dd($exception->getMessage());
            throw new \Exception('Student Advisor request failed!');
        }
    }
}
