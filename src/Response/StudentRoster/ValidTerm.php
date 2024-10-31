<?php

namespace Uisits\AitsApi\Response\StudentRoster;

use Spatie\LaravelData\Data;

class ValidTerm extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
