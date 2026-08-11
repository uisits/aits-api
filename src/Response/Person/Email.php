<?php

namespace Uisits\AitsApi\Response\Person;

use Spatie\LaravelData\Data;

class Email extends Data
{
    public function __construct(
        public ?string $emailAddress,
    ) {}
}