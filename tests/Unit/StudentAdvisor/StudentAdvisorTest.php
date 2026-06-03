<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AitsStudentAdvisor;
use Uisits\AitsApi\Response\StudentAdvisor\StudentAdvisor;

$studentPersonFixture = [
    'guid' => 'student-guid-001',
    'pidm' => '123456',
    'uin' => '650547906',
    'lastName' => 'Student',
    'firstName' => 'Test',
];

$advisorFixture = fn (string $primaryInd) => [
    'person' => [
        'guid' => 'advisor-guid-001',
        'pidm' => '654321',
        'uin' => '999999999',
        'lastName' => 'Advisor',
        'firstName' => 'Jane',
    ],
    'advisorTerm' => ['code' => '420248', 'description' => 'Fall 2024'],
    'primaryAdvisorInd' => $primaryInd,
    'advisorType' => ['code' => 'AC', 'description' => 'Academic Advisor'],
];

test('student advisor maps a successful response', function () use ($studentPersonFixture, $advisorFixture): void {
    Http::fake([
        'https://aits.test/student/StudentAdvisors/1_0/650547906/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryTermCode' => '420248',
                    'person' => $studentPersonFixture,
                    'advisors' => [$advisorFixture('Y')],
                ],
            ],
        ]),
    ]);

    $response = AitsStudentAdvisor::get('650547906', '420248');

    expect($response)
        ->toBeInstanceOf(StudentAdvisor::class)
        ->queryUIN->toBe('650547906')
        ->queryTermCode->toBe('420248')
        ->person->uin->toBe('650547906');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/student/StudentAdvisors/1_0/650547906/420248');
});

test('student advisor maps computed advisor properties from nested person', function () use ($studentPersonFixture, $advisorFixture): void {
    Http::fake([
        'https://aits.test/student/StudentAdvisors/1_0/650547906/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryTermCode' => '420248',
                    'person' => $studentPersonFixture,
                    'advisors' => [$advisorFixture('Y')],
                ],
            ],
        ]),
    ]);

    $advisor = AitsStudentAdvisor::get('650547906', '420248')->advisors->first();

    expect($advisor->firstName)->toBe('Jane')
        ->and($advisor->lastName)->toBe('Advisor')
        ->and($advisor->fullName)->toBe('Advisor Jane')
        ->and($advisor->uin)->toBe('999999999')
        ->and($advisor->pidm)->toBe('654321');
});

test('student advisor collection identifies the primary advisor', function () use ($studentPersonFixture, $advisorFixture): void {
    Http::fake([
        'https://aits.test/student/StudentAdvisors/1_0/650547906/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryTermCode' => '420248',
                    'person' => $studentPersonFixture,
                    'advisors' => [
                        $advisorFixture('N'),
                        $advisorFixture('Y'),
                    ],
                ],
            ],
        ]),
    ]);

    $advisors = AitsStudentAdvisor::get('650547906', '420248')->advisors;

    expect($advisors)->toHaveCount(2)
        ->and($advisors->primaryAdvisor()->primaryAdvisorInd)->toBe('Y')
        ->and($advisors->primaryAdvisor()->advisorType->code)->toBe('AC');
});

test('student advisor maps null advisors when no advisors assigned', function () use ($studentPersonFixture): void {
    Http::fake([
        'https://aits.test/student/StudentAdvisors/1_0/650547906/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryTermCode' => '420248',
                    'person' => $studentPersonFixture,
                    'advisors' => null,
                ],
            ],
        ]),
    ]);

    $response = AitsStudentAdvisor::get('650547906', '420248');

    expect($response->advisors)->toBeNull();
});

test('student advisor throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/student/StudentAdvisors/1_0/650547906/420248' => Http::response([], 500),
    ]);

    expect(fn () => AitsStudentAdvisor::get('650547906', '420248'))
        ->toThrow(AitsRequestFailed::class);
});

test('student advisor throws an exception when the list is empty', function (): void {
    Http::fake([
        'https://aits.test/student/StudentAdvisors/1_0/650547906/420248' => Http::response([
            'list' => [],
        ]),
    ]);

    expect(fn () => AitsStudentAdvisor::get('650547906', '420248'))
        ->toThrow(AitsRequestFailed::class);
});
