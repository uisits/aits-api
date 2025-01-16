<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzurePerson;

use Spatie\LaravelData\Data;

class Name extends Data
{
    public function __construct(
        public string $pidm,
        public string $uin,
        public ?string $type,
        public ?string $firstName,
        public ?string $middleName,
        public string $lastName,
    ) {}
}
