<?php

namespace Uisits\AitsApi\Response\StudentOverride;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Subject;

class Override extends Data
{
    public function __construct(
        public string $guid,
        public string $pidm,
        public string $termCode,
        public ?OverrideRule $rule,
        public ?Subject $subject,
        public string $courseNum,
        public string $sequenceNum,
        public string $crn,
        public string $user,
        public string $activityDate,
    ) {}
}
