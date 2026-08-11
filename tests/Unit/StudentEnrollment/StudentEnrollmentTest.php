<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AitsStudentEnrollment;
use Uisits\AitsApi\Response\StudentEnrollment\StudentEnrollment;

$enrollmentBaseFixture = fn (array $courseRegistration = []) => [
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
    'courseRegistration' => $courseRegistration,
];

$registeredCourseFixture = [
    'validRegistrationStatus' => ['code' => 'RW', 'description' => 'Registered Web'],
    'validGradingMode' => ['code' => 'Standard', 'description' => 'Standard Grading'],
    'validCourseRegistrationLevel' => ['code' => 'UG', 'description' => 'Undergraduate'],
    'courseSection' => [
        'courseReferenceNumber' => '15181',
        'validTerm' => ['code' => '420248', 'description' => 'Fall 2024'],
        'course' => [
            'courseSubjectAbbreviation' => 'CS',
            'courseNumber' => '101',
            'validCampus' => ['code' => 'UIC', 'description' => 'Chicago'],
            'courseTitle' => 'Intro to Programming',
            'validCollege' => ['code' => 'ENG', 'description' => 'Engineering'],
            'validDepartment' => ['code' => 'CS', 'description' => 'Computer Science'],
        ],
        'courseSectionSession' => [],
        'creditHours' => '3',
        'validPartTerm' => ['code' => '1', 'description' => 'Full Term'],
        'startDate' => '2024-08-26',
        'endDate' => '2024-12-14',
    ],
];

test('student enrollment maps a successful response', function () use ($enrollmentBaseFixture): void {
    Http::fake([
        'https://aits.test/student/StudentEnrollment/1_0/650547906/420248' => Http::response([
            'list' => [$enrollmentBaseFixture()],
        ]),
    ]);

    $response = AitsStudentEnrollment::get('650547906', '420248');

    expect($response)
        ->toBeInstanceOf(StudentEnrollment::class)
        ->validTerm->code->toBe('420248')
        ->validTerm->description->toBe('Fall 2024')
        ->validEnrollmentStatus->code->toBe('EL')
        ->lightweightPerson->institutionalId->toBe('650547906');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/student/StudentEnrollment/1_0/650547906/420248');
});

test('student enrollment maps lightweight person name fields', function () use ($enrollmentBaseFixture): void {
    Http::fake([
        'https://aits.test/student/StudentEnrollment/1_0/650547906/420248' => Http::response([
            'list' => [$enrollmentBaseFixture()],
        ]),
    ]);

    $response = AitsStudentEnrollment::get('650547906', '420248');

    expect($response->lightweightPerson->name->firstName)->toBe('Test')
        ->and($response->lightweightPerson->name->lastName)->toBe('Student')
        ->and($response->lightweightPerson->name->fullName)->toBe('Test Student');
});

test('student enrollment maps an empty course registration collection', function () use ($enrollmentBaseFixture): void {
    Http::fake([
        'https://aits.test/student/StudentEnrollment/1_0/650547906/420248' => Http::response([
            'list' => [$enrollmentBaseFixture([])],
        ]),
    ]);

    $response = AitsStudentEnrollment::get('650547906', '420248');

    expect($response->courseRegistration)->toBeEmpty();
});

test('student enrollment maps registered courses with section details', function () use ($enrollmentBaseFixture, $registeredCourseFixture): void {
    Http::fake([
        'https://aits.test/student/StudentEnrollment/1_0/650547906/420248' => Http::response([
            'list' => [$enrollmentBaseFixture([$registeredCourseFixture])],
        ]),
    ]);

    $response = AitsStudentEnrollment::get('650547906', '420248');
    $course = $response->courseRegistration->first();

    expect($response->courseRegistration)->toHaveCount(1)
        ->and($course->validRegistrationStatus->code)->toBe('RW')
        ->and($course->validGradingMode->code)->toBe('Standard')
        ->and($course->validCourseRegistrationLevel->code)->toBe('UG')
        ->and($course->courseSection->courseReferenceNumber)->toBe('15181')
        ->and($course->courseSection->creditHours)->toBe('3');
});

test('student enrollment maps course computed abbreviation property', function () use ($enrollmentBaseFixture, $registeredCourseFixture): void {
    Http::fake([
        'https://aits.test/student/StudentEnrollment/1_0/650547906/420248' => Http::response([
            'list' => [$enrollmentBaseFixture([$registeredCourseFixture])],
        ]),
    ]);

    $course = AitsStudentEnrollment::get('650547906', '420248')
        ->courseRegistration->first()
        ->courseSection->course;

    expect($course->courseSubjectAbbreviation)->toBe('CS')
        ->and($course->courseNumber)->toBe('101')
        ->and($course->course)->toBe('CS 101');
});

test('student enrollment throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/student/StudentEnrollment/1_0/650547906/420248' => Http::response([], 500),
    ]);

    expect(fn () => AitsStudentEnrollment::get('650547906', '420248'))
        ->toThrow(AitsRequestFailed::class);
});

test('student enrollment throws an exception when the list is empty', function (): void {
    Http::fake([
        'https://aits.test/student/StudentEnrollment/1_0/650547906/420248' => Http::response([
            'list' => [],
        ]),
    ]);

    expect(fn () => AitsStudentEnrollment::get('650547906', '420248'))
        ->toThrow(AitsRequestFailed::class, 'Student Enrollment not found!');
});
