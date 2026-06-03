<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AitsStudentOverride;
use Uisits\AitsApi\Response\StudentOverride\StudentOverride;

$personFixture = [
    'guid' => 'person-guid-001',
    'pidm' => '987654',
    'uin' => '650547906',
    'lastName' => 'Student',
    'firstName' => 'Test',
];

$overrideFixture = [
    'guid' => 'override-guid-001',
    'pidm' => '987654',
    'termCode' => '420248',
    'rule' => ['code' => 'PREQ', 'description' => 'Prerequisite Override'],
    'subject' => ['code' => 'CS', 'description' => 'Computer Science'],
    'courseNum' => '101',
    'sequenceNum' => '001',
    'crn' => '15001',
    'user' => 'ADMIN',
    'activityDate' => '2024-08-15',
];

test('student override get maps a successful response', function () use ($personFixture, $overrideFixture): void {
    Http::fake([
        'https://aits.test/student/StudentRegistrationOverrides/1_0/650547906/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryTermCode' => '420248',
                    'person' => $personFixture,
                    'overrides' => [$overrideFixture],
                ],
            ],
        ]),
    ]);

    $response = AitsStudentOverride::get('650547906', '420248');

    expect($response)
        ->toBeInstanceOf(StudentOverride::class)
        ->queryUIN->toBe('650547906')
        ->queryTermCode->toBe('420248')
        ->person->uin->toBe('650547906');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/student/StudentRegistrationOverrides/1_0/650547906/420248');
});

test('student override get maps override collection with rule fields', function () use ($personFixture, $overrideFixture): void {
    Http::fake([
        'https://aits.test/student/StudentRegistrationOverrides/1_0/650547906/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryTermCode' => '420248',
                    'person' => $personFixture,
                    'overrides' => [$overrideFixture],
                ],
            ],
        ]),
    ]);

    $override = AitsStudentOverride::get('650547906', '420248')->overrides->first();

    expect($override->crn)->toBe('15001')
        ->and($override->rule->code)->toBe('PREQ')
        ->and($override->rule->description)->toBe('Prerequisite Override')
        ->and($override->subject->code)->toBe('CS')
        ->and($override->termCode)->toBe('420248');
});

test('student override get maps activity date as a carbon instance', function () use ($personFixture, $overrideFixture): void {
    Http::fake([
        'https://aits.test/student/StudentRegistrationOverrides/1_0/650547906/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryTermCode' => '420248',
                    'person' => $personFixture,
                    'overrides' => [$overrideFixture],
                ],
            ],
        ]),
    ]);

    $override = AitsStudentOverride::get('650547906', '420248')->overrides->first();

    expect($override->activityDate)->toBeInstanceOf(\Carbon\Carbon::class)
        ->and($override->activityDate->format('Y-m-d'))->toBe('2024-08-15');
});

test('student override update sends a json payload and returns true', function (): void {
    Http::fake([
        'https://aits.test/student/StudentRegistrationOverride/1_0/' => Http::response([
            'result' => true,
        ]),
    ]);

    $result = AitsStudentOverride::update('420248', '987654', '15001', 'PREQ', 'Prerequisite Override');

    expect($result)->toBeTrue();

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/student/StudentRegistrationOverride/1_0/'
        && $request->method() === 'POST'
        && $request->data() === [
            'pidm' => '987654',
            'termCode' => '420248',
            'crn' => '15001',
            'rule' => [
                'code' => 'PREQ',
                'description' => 'Prerequisite Override',
            ],
        ]);
});

test('student override update handles special characters in description', function (): void {
    Http::fake([
        'https://aits.test/student/StudentRegistrationOverride/1_0/' => Http::response([
            'result' => true,
        ]),
    ]);

    $result = AitsStudentOverride::update('420248', '12345', '123', 'CODE', 'Needs "quoted" approval');

    expect($result)->toBeTrue();

    Http::assertSent(fn (Request $request): bool => $request->data()['rule']['description'] === 'Needs "quoted" approval');
});

test('student override update returns false when api returns failure status', function (): void {
    Http::fake([
        'https://aits.test/student/StudentRegistrationOverride/1_0/' => Http::response([], 422),
    ]);

    $result = AitsStudentOverride::update('420248', '987654', '15001', 'PREQ', 'Prerequisite Override');

    expect($result)->toBeFalse();
});

test('student override delete sends a post to the delete endpoint and returns true', function (): void {
    Http::fake([
        'https://aits.test/student/StudentRegistrationOverride/1_0/*' => Http::response([
            'result' => false,
        ]),
    ]);

    $result = AitsStudentOverride::delete('420248', '987654', '15001', 'PREQ', 'Prerequisite Override');

    expect($result)->toBeTrue();

    Http::assertSent(fn (Request $request): bool => str_contains($request->url(), 'StudentRegistrationOverride/1_0/')
        && str_contains($request->url(), 'delete=true')
        && $request->method() === 'POST'
        && $request->data() === [
            'pidm' => '987654',
            'termCode' => '420248',
            'crn' => '15001',
            'rule' => [
                'code' => 'PREQ',
                'description' => 'Prerequisite Override',
            ],
        ]);
});

test('student override delete returns false when api returns failure status', function (): void {
    Http::fake([
        'https://aits.test/student/StudentRegistrationOverride/1_0/*' => Http::response([], 422),
    ]);

    $result = AitsStudentOverride::delete('420248', '987654', '15001', 'PREQ', 'Prerequisite Override');

    expect($result)->toBeFalse();
});

test('student override get throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/student/StudentRegistrationOverrides/1_0/650547906/420248' => Http::response([], 500),
    ]);

    expect(fn () => AitsStudentOverride::get('650547906', '420248'))
        ->toThrow(AitsRequestFailed::class);
});

test('student override get throws an exception when the list is empty', function (): void {
    Http::fake([
        'https://aits.test/student/StudentRegistrationOverrides/1_0/650547906/420248' => Http::response([
            'list' => [],
        ]),
    ]);

    expect(fn () => AitsStudentOverride::get('650547906', '420248'))
        ->toThrow(AitsRequestFailed::class);
});
