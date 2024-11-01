<?php

namespace Uisits\AitsApi\Response;

use Spatie\LaravelData\Data;

class ValidTerm extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
