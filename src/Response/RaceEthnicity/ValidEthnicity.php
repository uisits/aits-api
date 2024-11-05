<?php

namespace Uisits\AitsApi\Response\RaceEthnicity;

use Spatie\LaravelData\Data;

class ValidEthnicity extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}