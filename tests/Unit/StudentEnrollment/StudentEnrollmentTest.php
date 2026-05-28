<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Request\AitsCourseDetail;
use Uisits\AitsApi\Request\AitsCourseSummary;
use Uisits\AitsApi\Request\AitsStudentEnrollment;
use Uisits\AitsApi\Request\AitsStudentHold;
use Uisits\AitsApi\Request\AitsStudentOverride;
use Uisits\AitsApi\Response\StudentEnrollment\StudentEnrollment;

test('student enrollment maps a successful response', function (): void {
    Http::fake([
        'https://aits.test/student/StudentEnrollment/1_0/650547906/420248' => Http::response([
            'list' => [
                [
                    'lightweightPerson' => [
                        'name' => [
                            'lastName' => 'Student',
                            'firstName' => 'Test',
                            'type' => 'LEGAL',
                        ],
                        'institutionalId' => '650547906',
                    ],
                    'validEnrollmentStatus' => [
                        'code' => 'EL',
                        'description' => 'Eligible',
                    ],
                    'validTerm' => [
                        'code' => '420248',
                        'description' => 'Fall 2024',
                    ],
                    'courseRegistration' => [],
                ],
            ],
        ]),
    ]);

    $response = AitsStudentEnrollment::get('650547906', '420248');

    expect($response)
        ->toBeInstanceOf(StudentEnrollment::class)
        ->validTerm->code->toBe('420248')
        ->lightweightPerson->institutionalId->toBe('650547906');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/student/StudentEnrollment/1_0/650547906/420248');
});

test('course summary returns an empty collection when list is empty', function (): void {
    Http::fake([
        'https://aits.test/student/CourseSummary/1_0/420248' => Http::response([
            'list' => [],
        ]),
    ]);

    $response = AitsCourseSummary::get('420248');

    expect($response)->toBeEmpty();
});

test('course detail request works when proxy is enabled without proxy values', function (): void {
    config()->set('aits-api.with_proxy', true);
    config()->set('aits-api.proxy.host', null);
    config()->set('aits-api.proxy.port', null);

    Http::fake([
        'https://aits.test/student/CourseDetail/1_0/420261/15181' => Http::response([
            'list' => [
                [
                    'term' => '420261',
                    'crn' => '15181',
                    'subject' => [
                        'code' => 'BIO',
                        'description' => 'Biology',
                    ],
                    'number' => '142',
                    'title' => 'General Biology II',
                    'sectionDescription' => 'This course is designed for science majors.',
                    'sectionStatus' => [
                        'code' => 'A',
                        'description' => 'Active',
                    ],
                    'scheduleType' => [
                        'code' => 'PKG',
                        'description' => 'Packaged Section',
                    ],
                    'gradableInd' => 'Y',
                    'sectionMaxEnrollment' => 16,
                    'sectionEnrollment' => 12,
                    'sectionAvailableSeats' => 4,
                    'sectionRoomNumber' => '2034',
                    'sectionMeetingDays' => 'TR',
                    'sectionMeetingHours' => '1400--1515',
                    'sectionMeetingDates' => '01/12/2026--05/02/2026',
                    'sectionBuildingDescription' => '2034 University Hall Bldg',
                    'instructor' => [
                        [
                            'uin' => '653108478',
                            'primaryInd' => 'N',
                            'firstName' => 'Charles',
                            'middleName' => 'Harold Caldwell',
                            'lastName' => 'Burroughs',
                            'sessionInstructorInd' => 'B1',
                        ],
                        [
                            'uin' => '660226220',
                            'primaryInd' => 'Y',
                            'firstName' => 'Rick',
                            'lastName' => 'Stokes',
                            'sessionInstructorInd' => 'L1',
                        ],
                    ],
                    'sectionNumber' => 'A',
                    'sectionPartOfTerm' => [
                        'code' => '1',
                        'description' => 'Full Term',
                    ],
                    'sectionMeetingType' => [
                        'code' => 'CLAS',
                        'description' => 'Class',
                    ],
                    'sectionMeetingScheduleType' => [
                        'code' => 'LCD',
                        'description' => 'Lecture-Discussion',
                    ],
                    'sectionWaitCapacity' => 0,
                    'sectionWaitCount' => 0,
                    'sectionWaitAvail' => 0,
                    'sectionBuilding' => [
                        'code' => '4UHB',
                        'description' => 'University Hall Bldg',
                    ],
                ],
                [
                    'term' => '420261',
                    'crn' => '15181',
                    'subject' => [
                        'code' => 'BIO',
                        'description' => 'Biology',
                    ],
                    'number' => '142',
                    'title' => 'General Biology II',
                    'sectionDescription' => 'This course is designed for science majors.',
                    'sectionStatus' => [
                        'code' => 'A',
                        'description' => 'Active',
                    ],
                    'scheduleType' => [
                        'code' => 'PKG',
                        'description' => 'Packaged Section',
                    ],
                    'gradableInd' => 'Y',
                    'sectionMaxEnrollment' => 16,
                    'sectionEnrollment' => 12,
                    'sectionAvailableSeats' => 4,
                    'sectionRoomNumber' => '261',
                    'sectionMeetingDays' => 'R',
                    'sectionMeetingHours' => '0900--1150',
                    'sectionMeetingDates' => '01/12/2026--05/02/2026',
                    'sectionBuildingDescription' => '261 Health and Sciences Build',
                    'instructor' => [
                        [
                            'uin' => '653108478',
                            'primaryInd' => 'N',
                            'firstName' => 'Charles',
                            'middleName' => 'Harold Caldwell',
                            'lastName' => 'Burroughs',
                            'sessionInstructorInd' => 'B1',
                        ],
                        [
                            'uin' => '660226220',
                            'primaryInd' => 'Y',
                            'firstName' => 'Rick',
                            'lastName' => 'Stokes',
                            'sessionInstructorInd' => 'L1',
                        ],
                    ],
                    'sectionNumber' => 'A',
                    'sectionPartOfTerm' => [
                        'code' => '1',
                        'description' => 'Full Term',
                    ],
                    'sectionMeetingType' => [
                        'code' => 'CLAS',
                        'description' => 'Class',
                    ],
                    'sectionMeetingScheduleType' => [
                        'code' => 'LAB',
                        'description' => 'Laboratory',
                    ],
                    'sectionWaitCapacity' => 0,
                    'sectionWaitCount' => 0,
                    'sectionWaitAvail' => 0,
                    'sectionBuilding' => [
                        'code' => '4HSB',
                        'description' => 'Health and Sciences Build',
                    ],
                ],
            ],
        ]),
    ]);

    $response = AitsCourseDetail::get('420261', '15181');

    expect($response)
        ->toHaveCount(2)
        ->and($response->first()->sectionMeetingScheduleType->code)->toBe('LCD')
        ->and($response->last()->sectionMeetingScheduleType->code)->toBe('LAB')
        ->and($response->first()->instructor)->toHaveCount(2)
        ->and($response->first()->instructor->primaryInstructor()->uin)->toBe('660226220');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/student/CourseDetail/1_0/420261/15181');
});

test('student hold put uses put and returns an empty collection', function (): void {
    Http::fake([
        'https://aits.test/student/StudentHolds/1_0/650547906' => Http::response([
            'list' => [],
        ]),
    ]);

    $response = AitsStudentHold::put('650547906');

    expect($response)->toBeEmpty();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PUT'
        && $request->url() === 'https://aits.test/student/StudentHolds/1_0/650547906');
});

test('student override update sends json-safe payload', function (): void {
    Http::fake([
        'https://aits.test/student/StudentRegistrationOverride/1_0/' => Http::response([
            'result' => true,
        ]),
    ]);

    $response = AitsStudentOverride::update('420248', '12345', '123', 'CODE', 'Needs "quoted" approval');

    expect($response)->toBeTrue();

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/student/StudentRegistrationOverride/1_0/'
        && $request->data() === [
            'pidm' => '12345',
            'termCode' => '420248',
            'crn' => '123',
            'rule' => [
                'code' => 'CODE',
                'description' => 'Needs "quoted" approval',
            ],
        ]);
});
