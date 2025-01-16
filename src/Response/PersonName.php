<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response;

use Spatie\LaravelData\Data;

class PersonName extends Data
{
    public function __construct(
        public string $lastName,
        public ?string $firstName,
        public ?string $type,
    ) {}
}
