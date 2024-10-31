<?php

namespace Uisits\AitsApi\Response\StudentAdvisor;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Person;

class Advisor extends Data
{
    public function __construct(
        public Person $person,
        public ?AdvisorTerm $advisorTerm,
        public string $primaryAdvisorInd,
        public ?AdvisorType $advisorType,
    ) {}
}
