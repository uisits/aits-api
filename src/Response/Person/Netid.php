<?php

namespace Uisits\AitsApi\Response\Person;

use Spatie\LaravelData\Data;

class Netid extends Data
{
    public function __construct(
        public string $netId,
        public string $campusDomain,
    ) {}
}
