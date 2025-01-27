<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\BasicLearner;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Person;

class BasicLearner extends Data
{
    public function __construct(
        public string $queryUIN,
        public string $queryTermCode,
        public Person $person,
        public StudentRecord $studentRecord,
        public StudentClass $studentClass,
    ) {}
}
