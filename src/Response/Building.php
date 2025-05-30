<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response;

use Spatie\LaravelData\Data;

class Building extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
