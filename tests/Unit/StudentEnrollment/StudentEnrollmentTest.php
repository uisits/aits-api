<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
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
