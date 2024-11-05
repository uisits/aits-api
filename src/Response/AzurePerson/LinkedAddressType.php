<?php

namespace Uisits\AitsApi\Response\AzurePerson;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class LinkedAddressType extends Data
{

    public function __construct(
        public string $code,
        public string $description,
    ) {}
}