<?php

namespace Uisits\AitsApi\Response\StudentEnrollment;

use Spatie\LaravelData\Data;

class ValidEnrollmentStatus extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}