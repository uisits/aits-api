<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\Person;

use Spatie\LaravelData\Data;

class Address extends Data
{
    public function __construct(
        public string $streetLine1,
        public ?string $streetLine2,
        public ?string $streetLine3,
        public string $city,
        public State $state,
        public string $zipCode,
    ) {}
}
