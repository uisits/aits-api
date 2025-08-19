<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request\AzureRequest;

use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Response\AzureBasicEmployee\AzureEmployee;
use Uisits\AitsApi\Response\AzureStudentGpa\AzureStudentGpa;

class AitsAzureStudentGpa
{
    /**
     * @return AzureEmployee
     *
     * @throws \Exception
     */
    public static function get(string $uin, string $termCode, string $level): \Spatie\LaravelData\Data
    {
        try {
            $response = Http::aitsAzure()
                ->get('/student/student-gpas-query/student-gpas-query/'.$uin.'/'.$termCode.'/'.$level);

            if (! $response->successful()) {
                throw new \Exception('StudentGpa request failed! '.$response);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new \Exception('StudentGpa not found!');
            }

            return AzureStudentGpa::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new \Exception('StudentGpa request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
