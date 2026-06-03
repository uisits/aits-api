<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AzureRequest\AitsAzureCourseDetail;
use Uisits\AitsApi\Response\CourseDetail\CourseDetail;

$courseDetailFixture = [
    'term' => '420248',
    'crn' => '15181',
    'subject' => ['code' => 'CS', 'description' => 'Computer Science'],
    'number' => '101',
    'title' => 'Intro to Programming',
    'sectionDescription' => null,
    'sectionStatus' => ['code' => 'A', 'description' => 'Active'],
    'scheduleType' => ['code' => 'LEC', 'description' => 'Lecture'],
    'gradableInd' => 'Y',
    'sectionMaxEnrollment' => 30,
    'sectionEnrollment' => 25,
    'sectionAvailableSeats' => 5,
    'sectionRoomNumber' => '101',
    'sectionMeetingDays' => 'MWF',
    'sectionMeetingHours' => '0900--0950',
    'sectionMeetingDates' => '08/26/2024--12/14/2024',
    'sectionBuildingDescription' => 'Science Hall',
    'instructor' => [
        [
            'uin' => '111111111',
            'primaryInd' => 'Y',
            'firstName' => 'Alice',
            'lastName' => 'Smith',
            'sessionInstructorInd' => 'L1',
        ],
    ],
    'sectionNumber' => '001',
    'sectionPartOfTerm' => ['code' => '1', 'description' => 'Full Term'],
    'sectionMeetingType' => ['code' => 'CLAS', 'description' => 'Class'],
    'sectionMeetingScheduleType' => ['code' => 'LEC', 'description' => 'Lecture'],
    'sectionWaitCapacity' => 0,
    'sectionWaitCount' => 0,
    'sectionWaitAvail' => 0,
    'sectionBuilding' => ['code' => 'SCI', 'description' => 'Science Hall'],
];

test('azure course detail get returns a collection of course detail objects', function () use ($courseDetailFixture): void {
    Http::fake([
        'https://aits.test/azure/student-course/course-detail-query/420248/15181' => Http::response([
            'list' => [$courseDetailFixture],
        ]),
    ]);

    $response = AitsAzureCourseDetail::get('420248', '15181');

    expect($response)
        ->toHaveCount(1)
        ->and($response->first())->toBeInstanceOf(CourseDetail::class)
        ->and($response->first()->term)->toBe('420248')
        ->and($response->first()->crn)->toBe('15181')
        ->and($response->first()->subject->code)->toBe('CS')
        ->and($response->first()->sectionEnrollment)->toBe(25);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/azure/student-course/course-detail-query/420248/15181');
});

test('azure course detail get returns an empty collection when list is empty', function (): void {
    Http::fake([
        'https://aits.test/azure/student-course/course-detail-query/420248/15181' => Http::response([
            'list' => [],
        ]),
    ]);

    $response = AitsAzureCourseDetail::get('420248', '15181');

    expect($response)->toBeEmpty();
});

test('azure course detail get sends the subscription key header', function () use ($courseDetailFixture): void {
    Http::fake([
        'https://aits.test/azure/student-course/course-detail-query/420248/15181' => Http::response([
            'list' => [$courseDetailFixture],
        ]),
    ]);

    AitsAzureCourseDetail::get('420248', '15181');

    Http::assertSent(fn (Request $request): bool => $request->header('Ocp-Apim-Subscription-Key')[0] === 'test-key');
});

test('azure course detail get maps instructor collection with primary instructor', function () use ($courseDetailFixture): void {
    Http::fake([
        'https://aits.test/azure/student-course/course-detail-query/420248/15181' => Http::response([
            'list' => [$courseDetailFixture],
        ]),
    ]);

    $instructor = AitsAzureCourseDetail::get('420248', '15181')
        ->first()
        ->instructor
        ->primaryInstructor()
        ->first();

    expect($instructor->uin)->toBe('111111111')
        ->and($instructor->firstName)->toBe('Alice');
});

test('azure course detail get throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/azure/student-course/course-detail-query/420248/15181' => Http::response([], 500),
    ]);

    expect(fn () => AitsAzureCourseDetail::get('420248', '15181'))
        ->toThrow(AitsRequestFailed::class);
});
