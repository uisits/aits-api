<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response;

use Spatie\LaravelData\Data;

class LightWeightPerson extends Data
{
    public function __construct(
        public PersonName $name,
        public string $institutionalId,
    ) {}
}
