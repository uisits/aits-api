<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentEnrollment;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Building;
use Uisits\AitsApi\Response\ScheduleType;

class CourseSectionSession extends Data
{
    public function __construct(
        public string $courseSectionSessionID,
        public ?string $meetsOnMondayFlag,
        /** @var Collection<int, CourseSessionInstructor> */
        public Collection $courseSessionInstructor,
        public ?string $startTime,
        public ?string $endTime,
        public ?Building $validBuilding,
        public ?string $room,
        public ScheduleType $validCourseScheduleType,
        public string $startDate,
        public string $endDate,
    ) {}
}
