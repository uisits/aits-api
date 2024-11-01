<?php

namespace Uisits\AitsApi\Response\Person;

use Spatie\LaravelData\Data;

class Phone extends Data
{
    public function __construct(
        public string $areaCode,
        public string $phoneNumber,
    ) {}
}
