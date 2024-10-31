<?php

namespace Uisits\AitsApi\Response\StudentOverride;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Person;

class StudentOverride extends Data
{
    public function __construct(
        public string $queryUIN,
        public string $queryTermCode,
        public Person $person,
        /** @var OverrideCollection<int, Override> */
        public OverrideCollection $overrides,
    ) {}
}
