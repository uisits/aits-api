<?php

namespace Uisits\AitsApi\Response\Person;

use Spatie\LaravelData\Data;

class Name extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
    ) {}
}
