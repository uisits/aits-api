<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\CourseDetail;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\ScheduleType;
use Uisits\AitsApi\Response\Subject;

class CourseDetail extends Data
{
    public function __construct(
        public string $term,
        public string $crn,
        public Subject $subject,
        public string $number,
        public string $title,
        public ?string $sectionDescription,
        public SectionStatus $sectionStatus,
        public ScheduleType $scheduleType,
        public string $gradableInd,
        public int $sectionMaxEnrollment,
        public int $sectionEnrollment,
        public int $sectionAvailableSeats,
        public ?string $crossListGroupID,
        public ?int $crossListSectionMaxEnrollment,
        public ?int $crossListSectionEnrollment,
        public ?int $crossListSectionAvailableSeats,
        public ?string $sectionRoomNumber,
        public ?string $sectionMeetingDays,
        public ?string $sectionMeetingHours,
        public ?string $sectionMeetingDates,
        public ?string $sectionBuildingDescription,
        /** @var InstructorCollection<int, Instructor> */
        public InstructorCollection $instructor,
        public string $sectionNumber,
        public ?SpecialApproval $specialApproval,
        public ?SectionPartOfTerm $sectionPartOfTerm,
        public ?SectionMeetingType $sectionMeetingType,
        public ?SectionMeetingScheduleType $sectionMeetingScheduleType,
        public ?SectionSession $sectionSession,
        public ?SectionBuilding $sectionBuilding,
        public ?int $sectionWaitAvail,
        public ?int $sectionWaitCapacity,
        public ?int $sectionWaitCount,
    ) {}
}
