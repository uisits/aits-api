<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentHold;

use Spatie\LaravelData\Data;

class HoldReason extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
