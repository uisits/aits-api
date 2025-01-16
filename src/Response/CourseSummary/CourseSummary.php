<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\CourseSummary;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Subject;

class CourseSummary extends Data
{
    public function __construct(
        public string $term,
        public string $crn,
        public Subject $subject,
        public string $number,
    ) {}
}
