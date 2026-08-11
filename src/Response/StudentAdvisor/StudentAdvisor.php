<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentAdvisor;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Person;

class StudentAdvisor extends Data
{
    public function __construct(
        public string $queryUIN,
        public string $queryTermCode,
        public Person $person,
        /** @var AdvisorCollection<int, Advisor> */
        public ?AdvisorCollection $advisors,
    ) {}
}
