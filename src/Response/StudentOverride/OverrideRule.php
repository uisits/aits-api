<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentOverride;

use Spatie\LaravelData\Data;

class OverrideRule extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
