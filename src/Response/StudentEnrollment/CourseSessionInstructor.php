<?php

namespace Uisits\AitsApi\Response\StudentEnrollment;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\LightWeightPerson;

class CourseSessionInstructor extends Data
{
    public function __construct(
        public LightWeightPerson $lightweightPerson,
        public string $emailAddress,
    ) {}
}
