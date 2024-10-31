<?php

namespace Uisits\AitsApi\Response\StudentHold;

use Spatie\LaravelData\Data;

class HoldOrigin extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
