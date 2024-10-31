<?php

namespace Uisits\AitsApi\Response\StudentRoster;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\LightWeightPerson;

class StudentRoster extends Data
{
    public function __construct(
        public ?LightWeightPerson $lightweightPerson,
        /** @var StudentRosterEmailCollection<int, StudentRosterEmail> */
        public StudentRosterEmailCollection $email,
        public string $courseReferenceNumber,
        public ValidTerm $validTerm,
        public ValidRegistrationStatus $validRegistrationStatus,
        public ValidCourseRegistrationLevel $validCourseRegistrationLevel,
    ) {}
}
