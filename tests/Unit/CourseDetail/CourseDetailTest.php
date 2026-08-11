<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AitsCourseDetail;
use Uisits\AitsApi\Response\CourseDetail\CourseDetail;

$sectionFixture = fn (string $scheduleTypeCode, string $scheduleTypeDesc, string $room) => [
    'term' => '420248',
    'crn' => '15181',
    'subject' => ['code' => 'CS', 'description' => 'Computer Science'],
    'number' => '101',
    'title' => 'Intro to Programming',
    'sectionDescription' => 'Learn the basics of programming.',
    'sectionStatus' => ['code' => 'A', 'description' => 'Active'],
    'scheduleType' => ['code' => 'LEC', 'description' => 'Lecture'],
    'gradableInd' => 'Y',
    'sectionMaxEnrollment' => 30,
    'sectionEnrollment' => 25,
    'sectionAvailableSeats' => 5,
    'sectionRoomNumber' => $room,
    'sectionMeetingDays' => 'MWF',
    'sectionMeetingHours' => '0900--0950',
    'sectionMeetingDates' => '01/12/2026--05/02/2026',
    'sectionBuildingDescription' => 'Science Building',
    'instructor' => [
        [
            'uin' => '111111111',
            'primaryInd' => 'Y',
            'firstName' => 'Alice',
            'lastName' => 'Smith',
            'sessionInstructorInd' => 'L1',
        ],
        [
            'uin' => '222222222',
            'primaryInd' => 'N',
            'firstName' => 'Bob',
            'lastName' => 'Jones',
            'sessionInstructorInd' => 'B1',
        ],
    ],
    'sectionNumber' => '001',
    'sectionPartOfTerm' => ['code' => '1', 'description' => 'Full Term'],
    'sectionMeetingType' => ['code' => 'CLAS', 'description' => 'Class'],
    'sectionMeetingScheduleType' => ['code' => $scheduleTypeCode, 'description' => $scheduleTypeDesc],
    'sectionWaitCapacity' => 5,
    'sectionWaitCount' => 2,
    'sectionWaitAvail' => 3,
    'sectionBuilding' => ['code' => 'SCI', 'description' => 'Science Building'],
];

test('course detail returns a collection of course detail objects', function () use ($sectionFixture): void {
    Http::fake([
        'https://aits.test/student/CourseDetail/1_0/420248/15181' => Http::response([
            'list' => [$sectionFixture('LCD', 'Lecture-Discussion', '101')],
        ]),
    ]);

    $response = AitsCourseDetail::get('420248', '15181');

    expect($response)
        ->toHaveCount(1)
        ->and($response->first())->toBeInstanceOf(CourseDetail::class)
        ->and($response->first()->term)->toBe('420248')
        ->and($response->first()->crn)->toBe('15181')
        ->and($response->first()->subject->code)->toBe('CS')
        ->and($response->first()->number)->toBe('101')
        ->and($response->first()->title)->toBe('Intro to Programming')
        ->and($response->first()->gradableInd)->toBe('Y')
        ->and($response->first()->sectionMaxEnrollment)->toBe(30)
        ->and($response->first()->sectionEnrollment)->toBe(25)
        ->and($response->first()->sectionAvailableSeats)->toBe(5);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/student/CourseDetail/1_0/420248/15181');
});

test('course detail returns an empty collection when list is empty', function (): void {
    Http::fake([
        'https://aits.test/student/CourseDetail/1_0/420248/15181' => Http::response([
            'list' => [],
        ]),
    ]);

    $response = AitsCourseDetail::get('420248', '15181');

    expect($response)->toBeEmpty();
});

test('course detail returns multiple sections for packaged or cross-listed courses', function () use ($sectionFixture): void {
    Http::fake([
        'https://aits.test/student/CourseDetail/1_0/420248/15181' => Http::response([
            'list' => [
                $sectionFixture('LCD', 'Lecture-Discussion', '101'),
                $sectionFixture('LAB', 'Laboratory', '201'),
            ],
        ]),
    ]);

    $response = AitsCourseDetail::get('420248', '15181');

    expect($response)->toHaveCount(2)
        ->and($response->first()->sectionMeetingScheduleType->code)->toBe('LCD')
        ->and($response->last()->sectionMeetingScheduleType->code)->toBe('LAB');
});

test('course detail instructor collection identifies the primary instructor', function () use ($sectionFixture): void {
    Http::fake([
        'https://aits.test/student/CourseDetail/1_0/420248/15181' => Http::response([
            'list' => [$sectionFixture('LCD', 'Lecture-Discussion', '101')],
        ]),
    ]);

    $response = AitsCourseDetail::get('420248', '15181');
    $instructors = $response->first()->instructor;

    expect($instructors)->toHaveCount(2)
        ->and($instructors->primaryInstructor()->first()->uin)->toBe('111111111')
        ->and($instructors->primaryInstructor()->first()->firstName)->toBe('Alice');
});

test('course detail instructor collection separates lecture and lab instructors', function () use ($sectionFixture): void {
    Http::fake([
        'https://aits.test/student/CourseDetail/1_0/420248/15181' => Http::response([
            'list' => [$sectionFixture('LCD', 'Lecture-Discussion', '101')],
        ]),
    ]);

    $instructors = AitsCourseDetail::get('420248', '15181')->first()->instructor;

    expect($instructors->getLectureInstructors())->toHaveCount(1)
        ->and($instructors->getLectureInstructors()->first()->uin)->toBe('111111111')
        ->and($instructors->getLabInstructors())->toHaveCount(1)
        ->and($instructors->getLabInstructors()->first()->uin)->toBe('222222222');
});

test('course detail maps wait list counts', function () use ($sectionFixture): void {
    Http::fake([
        'https://aits.test/student/CourseDetail/1_0/420248/15181' => Http::response([
            'list' => [$sectionFixture('LCD', 'Lecture-Discussion', '101')],
        ]),
    ]);

    $course = AitsCourseDetail::get('420248', '15181')->first();

    expect($course->sectionWaitCapacity)->toBe(5)
        ->and($course->sectionWaitCount)->toBe(2)
        ->and($course->sectionWaitAvail)->toBe(3);
});

test('course detail works when proxy is enabled without proxy host values', function () use ($sectionFixture): void {
    config()->set('aits-api.with_proxy', true);
    config()->set('aits-api.proxy.host', null);
    config()->set('aits-api.proxy.port', null);

    Http::fake([
        'https://aits.test/student/CourseDetail/1_0/420248/15181' => Http::response([
            'list' => [$sectionFixture('LCD', 'Lecture-Discussion', '101')],
        ]),
    ]);

    $response = AitsCourseDetail::get('420248', '15181');

    expect($response)->toHaveCount(1);
});

test('course detail throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/student/CourseDetail/1_0/420248/15181' => Http::response([], 500),
    ]);

    expect(fn () => AitsCourseDetail::get('420248', '15181'))
        ->toThrow(AitsRequestFailed::class);
});
