<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AzureRequest\AitsAzureStudentHighSchoolGpa;
use Uisits\AitsApi\Response\AzureStudentHsGpa\AzureStudentHsGpa;

$hsGpaFixture = [
    'ownerId' => 'owner-001',
    'userid' => 'tstudent',
    'sourceapplication' => 'BANNER',
    'validHighSchool' => [
        'type' => 'H',
        'highSchoolCode' => 'HS001',
        'highSchoolName' => 'Lincoln High School',
        'cityOrLocality' => 'Springfield',
        'stateOrProvince' => 'IL',
    ],
    'gpa' => '3.85',
    'classSize' => '250',
    'classRank' => '15',
    'percentile' => '94',
    'graduationDate' => '2022-05-15',
    'highSchoolSubject' => null,
    'requestName' => 'HS GPA Query',
    'diplomaName' => 'General Diploma',
    'diplomaType' => 'G',
];

test('azure student high school gpa get maps a successful response', function () use ($hsGpaFixture): void {
    Http::fake([
        'https://aits.test/azure/person/high-school-query/650547906' => Http::response([
            'list' => [$hsGpaFixture],
        ]),
    ]);

    $response = AitsAzureStudentHighSchoolGpa::get('650547906');

    expect($response)
        ->toBeInstanceOf(AzureStudentHsGpa::class)
        ->ownerId->toBe('owner-001')
        ->userid->toBe('tstudent')
        ->gpa->toBe('3.85')
        ->classSize->toBe('250')
        ->classRank->toBe('15')
        ->percentile->toBe('94')
        ->graduationDate->toBe('2022-05-15');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/azure/person/high-school-query/650547906');
});

test('azure student high school gpa get sends the subscription key header', function () use ($hsGpaFixture): void {
    Http::fake([
        'https://aits.test/azure/person/high-school-query/650547906' => Http::response([
            'list' => [$hsGpaFixture],
        ]),
    ]);

    AitsAzureStudentHighSchoolGpa::get('650547906');

    Http::assertSent(fn (Request $request): bool => $request->header('Ocp-Apim-Subscription-Key')[0] === 'test-key');
});

test('azure student high school gpa get maps high school details', function () use ($hsGpaFixture): void {
    Http::fake([
        'https://aits.test/azure/person/high-school-query/650547906' => Http::response([
            'list' => [$hsGpaFixture],
        ]),
    ]);

    $highSchool = AitsAzureStudentHighSchoolGpa::get('650547906')->validHighSchool;

    expect($highSchool->highSchoolCode)->toBe('HS001')
        ->and($highSchool->highSchoolName)->toBe('Lincoln High School')
        ->and($highSchool->cityOrLocality)->toBe('Springfield')
        ->and($highSchool->stateOrProvince)->toBe('IL')
        ->and($highSchool->type)->toBe('H');
});

test('azure student high school gpa get maps diploma information', function () use ($hsGpaFixture): void {
    Http::fake([
        'https://aits.test/azure/person/high-school-query/650547906' => Http::response([
            'list' => [$hsGpaFixture],
        ]),
    ]);

    $response = AitsAzureStudentHighSchoolGpa::get('650547906');

    expect($response->diplomaName)->toBe('General Diploma')
        ->and($response->diplomaType)->toBe('G');
});

test('azure student high school gpa get throws an exception when the list is empty', function (): void {
    Http::fake([
        'https://aits.test/azure/person/high-school-query/650547906' => Http::response([
            'list' => [],
        ]),
    ]);

    expect(fn () => AitsAzureStudentHighSchoolGpa::get('650547906'))
        ->toThrow(AitsRequestFailed::class, 'Student HighSchool Gpa not found!');
});

test('azure student high school gpa get throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/azure/person/high-school-query/650547906' => Http::response([], 500),
    ]);

    expect(fn () => AitsAzureStudentHighSchoolGpa::get('650547906'))
        ->toThrow(AitsRequestFailed::class);
});
