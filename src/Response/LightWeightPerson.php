<?php

namespace Uisits\AitsApi\Response;

use Spatie\LaravelData\Data;

class LightWeightPerson extends Data
{
    public function __construct(
        public array $name,
        public string $institutionalId,
    ) {}
}
