<?php

namespace Uisits\AitsApi\Response;

use Spatie\LaravelData\Data;

class Person extends Data
{
    public function __construct(
        public string $guid,
        public string $pidm,
        public string $uin,
        public ?string $lastName,
        public ?string $firstName,
    ) {}
}
