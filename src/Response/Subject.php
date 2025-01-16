<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response;

use Spatie\LaravelData\Data;

class Subject extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
