<?php

namespace Uisits\AitsApi\Response\StudentHold;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Person;

class StudentHold extends Data
{
    public function __construct(
        public string $queryUIN,
        public Person $person,
        /** @var HoldCollection<int, Hold> */
        public HoldCollection $hold,
    ) {}
}
