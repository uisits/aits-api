<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\CourseDetail;

use Spatie\LaravelData\Data;

class Instructor extends Data
{
    public function __construct(
        public string $uin,
        public string $primaryInd,
        public string $firstName,
        public string $middleName,
        public string $lastName,
    ) {}
}
