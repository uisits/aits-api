<?php

namespace Uisits\AitsApi\Response;

use Spatie\LaravelData\Data;

class ValidCourseRegistrationLevel extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
