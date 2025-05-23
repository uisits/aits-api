<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response;

use Spatie\LaravelData\Data;

class Department extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
