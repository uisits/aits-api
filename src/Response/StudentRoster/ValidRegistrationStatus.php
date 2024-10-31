<?php

namespace Uisits\AitsApi\Response\StudentRoster;

use Spatie\LaravelData\Data;

class ValidRegistrationStatus extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
