<?php

namespace Uisits\AitsApi\Response\AzurePerson;

use Spatie\LaravelData\Data;

class Identity extends Data
{

    public function __construct(
        public string $guid,
        public string $pidm,
        public string $uin,
    ) {}
}