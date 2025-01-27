<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentAdvisor;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Person;

class Advisor extends Data
{
    public ?string $firstName = null;

    public ?string $lastName = null;

    public ?string $uin = null;

    public ?string $pidm = null;

    public function __construct(
        public Person $person,
        public ?AdvisorTerm $advisorTerm,
        public string $primaryAdvisorInd,
        public ?AdvisorType $advisorType,
    ) {
        $this->firstName = $this->person?->firstName;
        $this->lastName = $this->person?->lastName;
        $this->uin = $this->person?->uin;
        $this->pidm = $this->person?->pidm;
    }
}
