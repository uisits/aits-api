<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\RaceEthnicity;

use Spatie\LaravelData\Data;

class ValidRace extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
