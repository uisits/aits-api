<?php

namespace Uisits\AitsApi\Response\StudentAttribute;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Person\Person;

class StudentAttribute extends Data
{
    public function __construct(
        public string $queryUIN,
        public string $queryCampusCode,
        public string $queryTermCode,
        public Person $person,
        /** @var Collection<int, Attribute> */
        public Collection $attribute,
    ) {}
}
