<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AzureRequest\AitsAzurePersonLookup;
use Uisits\AitsApi\Response\AzurePerson\AzurePerson;

$azurePersonFixture = [
    'identity' => [
        'guid' => 'person-guid-001',
        'pidm' => '987654',
        'uin' => '650547906',
    ],
    'names' => [
        [
            'pidm' => '987654',
            'uin' => '650547906',
            'type' => 'LEGAL',
            'firstName' => 'Test',
            'middleName' => null,
            'lastName' => 'Student',
        ],
        [
            'pidm' => '987654',
            'uin' => '650547906',
            'type' => 'PREFERRED',
            'firstName' => 'Tess',
            'middleName' => null,
            'lastName' => 'Student',
        ],
    ],
    'biodemo' => [
        'guid' => 'bio-guid-001',
        'pidm' => '987654',
        'activityDate' => null,
        'dataOrigin' => null,
        'userId' => null,
        'birthDate' => '1999-05-15',
        'citzCode' => 'US',
        'maritalStatus' => 'S',
        'gender' => 'M',
        'confidentialInd' => 'N',
        'genderIdentity' => null,
        'personalPronoun' => null,
        'armedForcesServiceMedal' => null,
    ],
    'address' => [
        [
            'guid' => 'addr-guid-001',
            'pidm' => '987654',
            'fromDate' => '2024-01-01',
            'toDate' => null,
            'activityDate' => null,
            'statusInd' => null,
            'type' => ['code' => 'MA', 'description' => 'Mailing'],
            'sequenceNum' => null,
            'streetLine1' => '1200 W Harrison St',
            'streetLine2' => null,
            'streetLine3' => null,
            'city' => 'Chicago',
            'state' => ['code' => 'IL', 'description' => 'Illinois'],
            'zipCode' => '60607',
            'county' => null,
            'nation' => null,
            'effectiveStatus' => null,
            'phone' => null,
        ],
    ],
    'email' => [
        [
            'guid' => 'email-guid-001',
            'pidm' => '987654',
            'activityDate' => null,
            'emailCode' => 'UINSTI',
            'emailAddress' => 'tstudent@uic.edu',
            'statusInd' => null,
            'preferredInd' => 'Y',
            'displayWebInd' => 'Y',
            'dataOrigin' => null,
            'effectiveStatus' => null,
        ],
    ],
    'phone' => [
        [
            'guid' => 'phone-guid-001',
            'pidm' => '987654',
            'sequenceNum' => '1',
            'type' => ['code' => 'CELL', 'description' => 'Cell Phone'],
            'activityDate' => null,
            'linkedAddressType' => null,
            'linkedAddressSequence' => null,
            'primaryInd' => 'Y',
            'internationalAccessCode' => null,
            'effectiveStatus' => null,
        ],
    ],
    'employee' => null,
];

test('azure person lookup get maps a successful response', function () use ($azurePersonFixture): void {
    Http::fake([
        'https://aits.test/azure/person/person-data-query/650547906' => Http::response([
            'list' => [$azurePersonFixture],
        ]),
    ]);

    $response = AitsAzurePersonLookup::get('650547906');

    expect($response)
        ->toBeInstanceOf(AzurePerson::class)
        ->identity->guid->toBe('person-guid-001')
        ->identity->uin->toBe('650547906');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/azure/person/person-data-query/650547906');
});

test('azure person lookup get sends the subscription key header', function () use ($azurePersonFixture): void {
    Http::fake([
        'https://aits.test/azure/person/person-data-query/650547906' => Http::response([
            'list' => [$azurePersonFixture],
        ]),
    ]);

    AitsAzurePersonLookup::get('650547906');

    Http::assertSent(fn (Request $request): bool => $request->header('Ocp-Apim-Subscription-Key')[0] === 'test-key');
});

test('azure person lookup get maps multiple name entries', function () use ($azurePersonFixture): void {
    Http::fake([
        'https://aits.test/azure/person/person-data-query/650547906' => Http::response([
            'list' => [$azurePersonFixture],
        ]),
    ]);

    $response = AitsAzurePersonLookup::get('650547906');

    expect($response->names)->toHaveCount(2)
        ->and($response->names->first()->type)->toBe('LEGAL')
        ->and($response->names->first()->firstName)->toBe('Test')
        ->and($response->names->last()->type)->toBe('PREFERRED');
});

test('azure person lookup get maps biographical data', function () use ($azurePersonFixture): void {
    Http::fake([
        'https://aits.test/azure/person/person-data-query/650547906' => Http::response([
            'list' => [$azurePersonFixture],
        ]),
    ]);

    $bio = AitsAzurePersonLookup::get('650547906')->biodemo;

    expect($bio->birthDate)->toBe('1999-05-15')
        ->and($bio->gender)->toBe('M')
        ->and($bio->citzCode)->toBe('US');
});

test('azure person lookup get maps email collection', function () use ($azurePersonFixture): void {
    Http::fake([
        'https://aits.test/azure/person/person-data-query/650547906' => Http::response([
            'list' => [$azurePersonFixture],
        ]),
    ]);

    $email = AitsAzurePersonLookup::get('650547906')->email->first();

    expect($email->emailAddress)->toBe('tstudent@uic.edu')
        ->and($email->preferredInd)->toBe('Y');
});

test('azure person lookup get throws an exception when the list is empty', function (): void {
    Http::fake([
        'https://aits.test/azure/person/person-data-query/650547906' => Http::response([
            'list' => [],
        ]),
    ]);

    expect(fn () => AitsAzurePersonLookup::get('650547906'))
        ->toThrow(AitsRequestFailed::class, 'Person not found!');
});

test('azure person lookup get throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/azure/person/person-data-query/650547906' => Http::response([], 500),
    ]);

    expect(fn () => AitsAzurePersonLookup::get('650547906'))
        ->toThrow(AitsRequestFailed::class);
});
