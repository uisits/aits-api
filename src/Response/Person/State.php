<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\Person;

use Spatie\LaravelData\Data;

class State extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
