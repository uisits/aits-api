<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentEnrollment;

use Spatie\LaravelData\Data;

class ValidGradingMode extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
