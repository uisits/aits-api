<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AzureRequest\AitsAzureStudentGpa;
use Uisits\AitsApi\Response\AzureStudentGpa\AzureStudentGpa;

$gpaFixture = fn (float $gpaValue, float $hours) => [
    'guid' => 'gpa-guid-001',
    'pidm' => '987654',
    'term' => ['code' => '420248', 'description' => 'Fall 2024'],
    'level' => ['code' => 'UG', 'description' => 'Undergraduate'],
    'typeInd' => 'I',
    'hoursAttempted' => $hours,
    'hoursPassed' => $hours,
    'hoursEarned' => $hours,
    'hours' => $hours,
    'gpa' => $gpaValue,
    'activityDate' => '2024-12-15',
];

$studentGpaFixture = [
    'queryUIN' => '650547906',
    'queryTermCode' => '420248',
    'queryLevelCode' => 'UG',
    'person' => [
        'guid' => 'person-guid-001',
        'pidm' => '987654',
        'uin' => '650547906',
        'lastName' => 'Student',
        'firstName' => 'Test',
    ],
    'termInstitutionalGpa' => null,
    'levelInstitutionalGpa' => null,
    'levelOverallGpa' => null,
    'levelTransferGpa' => null,
];

test('azure student gpa get maps a successful response', function () use ($studentGpaFixture): void {
    Http::fake([
        'https://aits.test/azure/student/student-gpas-query/student-gpas-query/650547906/420248/UG' => Http::response([
            'list' => [$studentGpaFixture],
        ]),
    ]);

    $response = AitsAzureStudentGpa::get('650547906', '420248', 'UG');

    expect($response)
        ->toBeInstanceOf(AzureStudentGpa::class)
        ->queryUIN->toBe('650547906')
        ->queryTermCode->toBe('420248')
        ->queryLevelCode->toBe('UG');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/azure/student/student-gpas-query/student-gpas-query/650547906/420248/UG');
});

test('azure student gpa get sends the subscription key header', function () use ($studentGpaFixture): void {
    Http::fake([
        'https://aits.test/azure/student/student-gpas-query/student-gpas-query/650547906/420248/UG' => Http::response([
            'list' => [$studentGpaFixture],
        ]),
    ]);

    AitsAzureStudentGpa::get('650547906', '420248', 'UG');

    Http::assertSent(fn (Request $request): bool => $request->header('Ocp-Apim-Subscription-Key')[0] === 'test-key');
});

test('azure student gpa get maps all four gpa type fields', function () use ($studentGpaFixture, $gpaFixture): void {
    Http::fake([
        'https://aits.test/azure/student/student-gpas-query/student-gpas-query/650547906/420248/UG' => Http::response([
            'list' => [
                array_merge($studentGpaFixture, [
                    'termInstitutionalGpa' => $gpaFixture(3.75, 15.0),
                    'levelInstitutionalGpa' => $gpaFixture(3.60, 60.0),
                    'levelOverallGpa' => $gpaFixture(3.55, 75.0),
                    'levelTransferGpa' => $gpaFixture(3.20, 15.0),
                ]),
            ],
        ]),
    ]);

    $response = AitsAzureStudentGpa::get('650547906', '420248', 'UG');

    expect($response->termInstitutionalGpa->gpa)->toBe(3.75)
        ->and($response->termInstitutionalGpa->hours)->toBe(15.0)
        ->and($response->levelInstitutionalGpa->gpa)->toBe(3.60)
        ->and($response->levelOverallGpa->gpa)->toBe(3.55)
        ->and($response->levelTransferGpa->gpa)->toBe(3.20);
});

test('azure student gpa get maps null gpa fields when not present', function () use ($studentGpaFixture): void {
    Http::fake([
        'https://aits.test/azure/student/student-gpas-query/student-gpas-query/650547906/420248/UG' => Http::response([
            'list' => [$studentGpaFixture],
        ]),
    ]);

    $response = AitsAzureStudentGpa::get('650547906', '420248', 'UG');

    expect($response->termInstitutionalGpa)->toBeNull()
        ->and($response->levelInstitutionalGpa)->toBeNull()
        ->and($response->levelOverallGpa)->toBeNull()
        ->and($response->levelTransferGpa)->toBeNull();
});

test('azure student gpa get maps the person identity fields', function () use ($studentGpaFixture): void {
    Http::fake([
        'https://aits.test/azure/student/student-gpas-query/student-gpas-query/650547906/420248/UG' => Http::response([
            'list' => [$studentGpaFixture],
        ]),
    ]);

    $response = AitsAzureStudentGpa::get('650547906', '420248', 'UG');

    expect($response->person->uin)->toBe('650547906')
        ->and($response->person->pidm)->toBe('987654');
});

test('azure student gpa get throws an exception when the list is empty', function (): void {
    Http::fake([
        'https://aits.test/azure/student/student-gpas-query/student-gpas-query/650547906/420248/UG' => Http::response([
            'list' => [],
        ]),
    ]);

    expect(fn () => AitsAzureStudentGpa::get('650547906', '420248', 'UG'))
        ->toThrow(AitsRequestFailed::class, 'StudentGpa not found!');
});

test('azure student gpa get throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/azure/student/student-gpas-query/student-gpas-query/650547906/420248/UG' => Http::response([], 500),
    ]);

    expect(fn () => AitsAzureStudentGpa::get('650547906', '420248', 'UG'))
        ->toThrow(AitsRequestFailed::class);
});
