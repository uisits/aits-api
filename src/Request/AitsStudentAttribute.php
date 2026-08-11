<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Request;

use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Response\StudentAttribute\StudentAttribute;

class AitsStudentAttribute
{
    /**
     * @return StudentAttribute
     *
     * @throws \Exception
     */
    public static function get(string $uin, string $term): Data
    {
        try {
            $response = Http::aits()
                ->get('/StudentAttributes/1_0/'.$uin.'/400/'.$term);

            if (! $response->successful()) {
                throw new AitsRequestFailed('Student Attribute request failed! '.$response);
            }

            if ($response->collect('list')->isEmpty()) {
                throw new AitsRequestFailed('Student Attribute not found!', 404);
            }

            return StudentAttribute::from($response->collect('list')->first());
        } catch (\Exception $exception) {
            throw new AitsRequestFailed('Student Attribute request failed! '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
