<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request\AzureRequest;

use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Response\AzureStudentGpa\AzureStudentGpa;

class AitsAzureStudentGpa
{
    /**
     * @param string $uin
     * @param string $termCode
     * @param string $level
     * @return AzureStudentGpa
     *
     * @throws AitsRequestFailed
     */
    public static function get(string $uin, string $termCode, string $level): AzureStudentGpa
    {
        try {
            $response = Http::aitsAzure()
                ->get('/student/student-gpas-query/student-gpas-query/'.$uin.'/'.$termCode.'/'.$level);

            if (! $response->successful()) {
                throw new AitsRequestFailed('StudentGpa request failed! '.$response, 500);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new AitsRequestFailed('StudentGpa not found!', 404);
            }

            return AzureStudentGpa::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('StudentGpa request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
