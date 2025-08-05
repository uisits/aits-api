<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentAdvisor;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Person;

class Advisor extends Data
{
    #[Computed]
    public ?string $firstName;

    #[Computed]
    public ?string $lastName;

    #[Computed]
    public ?string $fullName;

    #[Computed]
    public ?string $uin;

    #[Computed]
    public ?string $pidm;

    public function __construct(
        public Person $person,
        public ?AdvisorTerm $advisorTerm,
        public ?string $primaryAdvisorInd,
        public ?AdvisorType $advisorType,
    ) {
        $this->firstName = $this->person?->firstName;
        $this->lastName = $this->person?->lastName;
        $this->fullName = $this->person?->lastName . ' ' . $this->person?->firstName;
        $this->uin = $this->person?->uin;
        $this->pidm = $this->person?->pidm;
    }
}
