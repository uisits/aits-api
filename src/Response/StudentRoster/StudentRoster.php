<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentRoster;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\LightWeightPerson;
use Uisits\AitsApi\Response\ValidCourseRegistrationLevel;
use Uisits\AitsApi\Response\ValidRegistrationStatus;
use Uisits\AitsApi\Response\ValidTerm;

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
