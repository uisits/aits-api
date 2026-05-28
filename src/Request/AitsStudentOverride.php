<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Response\StudentOverride\StudentOverride;

class AitsStudentOverride
{
    /**
     * @return StudentOverride
     *
     * @throws \Exception
     */
    public static function get(string $uin, string $term): Data
    {
        try {
            $response = Http::aits()
                ->get('/StudentRegistrationOverrides/1_0/'.$uin.'/'.$term);

            if (! $response->successful()) {
                throw new AitsRequestFailed('Student Override request failed!', 500);
            }

            return StudentOverride::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Student Override request failed!', $exception->getCode(), $exception);
        }
    }

    /**
     * @throws \Exception
     */
    public static function update(
        string $term, string $pidm, string $crn,
        string $overrideCode, string $overrideDescription
    ): bool {
        $body = [
            'pidm' => $pidm,
            'termCode' => $term,
            'crn' => $crn,
            'rule' => [
                'code' => $overrideCode,
                'description' => $overrideDescription,
            ],
        ];

        try {
            $response = Http::aits()
                ->post('/StudentRegistrationOverride/1_0/', $body);

            if (! $response->successful()) {
                return false;
            }

            return $response->json()['result'];
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Student Override Update request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws \Exception
     */
    public static function delete(
        string $term, string $pidm, string $crn,
        string $overrideCode, string $overrideDescription
    ): bool {
        $body = [
            'pidm' => $pidm,
            'termCode' => $term,
            'crn' => $crn,
            'rule' => [
                'code' => $overrideCode,
                'description' => $overrideDescription,
            ],
        ];

        try {
            $response = Http::aits()
                ->post('/StudentRegistrationOverride/1_0/?delete=true', $body);

            if (! $response->successful()) {
                return false;
            }

            return ! $response->json()['result'];
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Student Override Delete request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
