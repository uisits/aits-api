<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response;

use Spatie\LaravelData\Data;

class ScheduleType extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
