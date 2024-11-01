<?php

namespace Uisits\AitsApi\Response\StudentEnrollment;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\LightWeightPerson;
use Uisits\AitsApi\Response\ValidTerm;

class StudentEnrollment extends Data
{
    public function __construct(
        public LightWeightPerson $lightweightPerson,
        public ValidEnrollmentStatus $validEnrollmentStatus,
        public ValidTerm $validTerm,
        /** @var RegisteredCourseCollection<int, RegisteredCourse> */
        public RegisteredCourseCollection $courseRegistration,
    ) {}
}
