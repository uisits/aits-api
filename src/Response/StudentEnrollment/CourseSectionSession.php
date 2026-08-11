<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentEnrollment;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Building;
use Uisits\AitsApi\Response\ScheduleType;

class CourseSectionSession extends Data
{
    public function __construct(
        public string $courseSectionSessionID,
        public ?string $meetsOnMondayFlag,
        public ?string $meetsOnTuesdayFlag,
        public ?string $meetsOnWednesdayFlag,
        public ?string $meetsOnThursdayFlag,
        public ?string $meetsOnFridayFlag,
        public ?string $meetsOnSaturdayFlag,
        public ?string $meetsOnSundayFlag,
        /** @var Collection<int, CourseSessionInstructor> */
        public Collection $courseSessionInstructor,
        public ?string $startTime,
        public ?string $endTime,
        public ?Building $validBuilding,
        public ?string $room,
        public ScheduleType $validCourseScheduleType,
        #[WithCast(DateTimeInterfaceCast::class, format: 'M j, Y, h:i:s A')]
        public ?Carbon $startDate,
        #[WithCast(DateTimeInterfaceCast::class, format: 'M j, Y, h:i:s A')]
        public ?Carbon $endDate,
    ) {}
}
