<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AzureRequest\AitsAzureBasicEmployee;
use Uisits\AitsApi\Response\AzureBasicEmployee\AzureEmployee;

$employeeFixture = [
    'status' => 'A',
    'workPeriod' => 'AY',
    'userid' => 'jsmith',
    'timeStatus' => 'F',
    'group' => 'FACULTY',
    'sourceApplication' => 'BANNER',
    'flsa' => 'E',
    'institutionalId' => '650547906',
    'validCampus' => ['code' => 'UIC', 'description' => 'Chicago'],
    'homeOrganization' => [
        'validChartOfAccounts' => ['code' => 'UIC', 'description' => 'UIC Chart'],
        'validOrganization' => ['code' => 'CS', 'description' => 'Computer Science'],
    ],
    'distributionOrganization' => null,
    'validEmployeeClass' => ['code' => 'AP', 'description' => 'Academic Professional'],
    'validLeaveCategory' => ['code' => 'FT', 'description' => 'Full Time'],
    'validBenefitCategory' => ['code' => 'A', 'description' => 'Full Benefit'],
    'employmentDates' => null,
    'employmentLeave' => null,
    'employmentTermination' => null,
    'eVerifyCaseNumber' => null,
    'eVerifyEffectiveDate' => null,
    'homeCollege' => ['code' => 'ENG', 'description' => 'Engineering'],
];

test('azure basic employee get maps a successful response', function () use ($employeeFixture): void {
    Http::fake([
        'https://aits.test/azure/employee/basic-employee-query/650547906' => Http::response([
            'list' => [$employeeFixture],
        ]),
    ]);

    $response = AitsAzureBasicEmployee::get('650547906');

    expect($response)
        ->toBeInstanceOf(AzureEmployee::class)
        ->status->toBe('A')
        ->userid->toBe('jsmith')
        ->timeStatus->toBe('F')
        ->institutionalId->toBe('650547906')
        ->flsa->toBe('E');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/azure/employee/basic-employee-query/650547906');
});

test('azure basic employee get sends the subscription key header', function () use ($employeeFixture): void {
    Http::fake([
        'https://aits.test/azure/employee/basic-employee-query/650547906' => Http::response([
            'list' => [$employeeFixture],
        ]),
    ]);

    AitsAzureBasicEmployee::get('650547906');

    Http::assertSent(fn (Request $request): bool => $request->header('Ocp-Apim-Subscription-Key')[0] === 'test-key');
});

test('azure basic employee get maps campus and organization', function () use ($employeeFixture): void {
    Http::fake([
        'https://aits.test/azure/employee/basic-employee-query/650547906' => Http::response([
            'list' => [$employeeFixture],
        ]),
    ]);

    $response = AitsAzureBasicEmployee::get('650547906');

    expect($response->validCampus->code)->toBe('UIC')
        ->and($response->homeOrganization->validOrganization->code)->toBe('CS')
        ->and($response->validEmployeeClass->code)->toBe('AP')
        ->and($response->homeCollege->code)->toBe('ENG');
});

test('azure basic employee get maps nullable fields as null when absent', function () use ($employeeFixture): void {
    Http::fake([
        'https://aits.test/azure/employee/basic-employee-query/650547906' => Http::response([
            'list' => [$employeeFixture],
        ]),
    ]);

    $response = AitsAzureBasicEmployee::get('650547906');

    expect($response->employmentDates)->toBeNull()
        ->and($response->employmentLeave)->toBeNull()
        ->and($response->employmentTermination)->toBeNull()
        ->and($response->eVerifyCaseNumber)->toBeNull();
});

test('azure basic employee get throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/azure/employee/basic-employee-query/650547906' => Http::response([], 500),
    ]);

    expect(fn () => AitsAzureBasicEmployee::get('650547906'))
        ->toThrow(AitsRequestFailed::class);
});
