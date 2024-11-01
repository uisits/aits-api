<?php

namespace Uisits\AitsApi\Response\StudentEnrollment;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\ValidPartTerm;
use Uisits\AitsApi\Response\ValidTerm;

class CourseSection extends Data
{
    public function __construct(
        public string $courseReferenceNumber,
        public ValidTerm $validTerm,
        public Course $course,
        /** @var CourseSectionSessionCollection<int, CourseSectionSession> */
        public CourseSectionSessionCollection $courseSectionSession,
        public string $creditHours,
        public ValidPartTerm $validPartTerm,
        public string $startDate,
        public string $endDate,
    ) {}
}
