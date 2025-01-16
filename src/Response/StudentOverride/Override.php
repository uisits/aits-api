<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentOverride;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Subject;

class Override extends Data
{
    public function __construct(
        public string $guid,
        public string $pidm,
        public string $termCode,
        public ?OverrideRule $overrideRule,
        public ?Subject $subject,
        public string $courseNum,
        public string $sequenceNum,
        public string $crn,
        public string $user,
        public string $activityDate,
    ) {}
}
