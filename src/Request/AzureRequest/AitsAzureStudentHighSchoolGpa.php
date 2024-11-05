<?php

namespace Uisits\AitsApi\Request\AzureRequest;

use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Response\AzurePerson\AzureStudentGpa;
use Uisits\AitsApi\Response\RaceEthnicity\RaceEthnicity;

class AitsAzureStudentHighSchoolGpa
{
    /**
     * @return AzureStudentGpa
     *
     * @throws \Exception
     */
    public static function get(string $uin)
    {
        try {
            $response = Http::aitsAzure()
                ->get('/person/high-school-query/high-school-query/' . $uin);

            if (!$response->successful()) {
                throw new \Exception('Student HighSchool Gpa request failed! ' . $response);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new \Exception('Student HighSchool Gpa not found!');
            }

            dd($response->collect('list')->first());
            return AzureStudentGpa::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new \Exception('Student HighSchool Gpa request failed! ' . $exception->getMessage());
        }
    }
}
