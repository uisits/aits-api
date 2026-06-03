<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AzureRequest\AitsAzureRaceEthnicity;
use Uisits\AitsApi\Response\RaceEthnicity\RaceEthnicity;

$raceEthnicityFixture = [
    'pidm' => '987654',
    'uin' => '650547906',
    'oldEthnicity' => 'H',
    'validEthnicity' => [
        'code' => 'H',
        'description' => 'Hispanic or Latino',
    ],
    'race' => [
        [
            'validRace' => ['code' => 'WH', 'description' => 'White'],
            'userId' => 'BANNER',
            'dataOrigin' => 'CANVAS',
            'activityDatetime' => '2023-09-01T00:00:00Z',
            'validRegulatoryRace' => ['code' => 'WH', 'description' => 'White'],
        ],
        [
            'validRace' => ['code' => 'AS', 'description' => 'Asian'],
            'userId' => 'BANNER',
            'dataOrigin' => 'CANVAS',
            'activityDatetime' => '2023-09-01T00:00:00Z',
            'validRegulatoryRace' => ['code' => 'AS', 'description' => 'Asian'],
        ],
    ],
    'raceEthnicityConfirmation' => [
        'confirmedIndicator' => 'Y',
        'confirmedDate' => '2023-09-01T00:00:00+00:00',
    ],
];

test('azure race ethnicity get maps a successful response', function () use ($raceEthnicityFixture): void {
    Http::fake([
        'https://aits.test/azure/person/race-ethnicity-query/650547906' => Http::response([
            'list' => [$raceEthnicityFixture],
        ]),
    ]);

    $response = AitsAzureRaceEthnicity::get('650547906');

    expect($response)
        ->toBeInstanceOf(RaceEthnicity::class)
        ->pidm->toBe('987654')
        ->uin->toBe('650547906')
        ->oldEthnicity->toBe('H');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/azure/person/race-ethnicity-query/650547906');
});

test('azure race ethnicity get sends the subscription key header', function () use ($raceEthnicityFixture): void {
    Http::fake([
        'https://aits.test/azure/person/race-ethnicity-query/650547906' => Http::response([
            'list' => [$raceEthnicityFixture],
        ]),
    ]);

    AitsAzureRaceEthnicity::get('650547906');

    Http::assertSent(fn (Request $request): bool => $request->header('Ocp-Apim-Subscription-Key')[0] === 'test-key');
});

test('azure race ethnicity get maps valid ethnicity', function () use ($raceEthnicityFixture): void {
    Http::fake([
        'https://aits.test/azure/person/race-ethnicity-query/650547906' => Http::response([
            'list' => [$raceEthnicityFixture],
        ]),
    ]);

    $response = AitsAzureRaceEthnicity::get('650547906');

    expect($response->validEthnicity->code)->toBe('H')
        ->and($response->validEthnicity->description)->toBe('Hispanic or Latino');
});

test('azure race ethnicity get maps race collection', function () use ($raceEthnicityFixture): void {
    Http::fake([
        'https://aits.test/azure/person/race-ethnicity-query/650547906' => Http::response([
            'list' => [$raceEthnicityFixture],
        ]),
    ]);

    $response = AitsAzureRaceEthnicity::get('650547906');

    expect($response->race)->toHaveCount(2)
        ->and($response->race->first()->validRace->code)->toBe('WH')
        ->and($response->race->last()->validRace->code)->toBe('AS');
});

test('azure race ethnicity get maps confirmation status', function () use ($raceEthnicityFixture): void {
    Http::fake([
        'https://aits.test/azure/person/race-ethnicity-query/650547906' => Http::response([
            'list' => [$raceEthnicityFixture],
        ]),
    ]);

    $confirmation = AitsAzureRaceEthnicity::get('650547906')->raceEthnicityConfirmation;

    expect($confirmation->confirmedIndicator)->toBe('Y');
});

test('azure race ethnicity get throws an exception when the list is empty', function (): void {
    Http::fake([
        'https://aits.test/azure/person/race-ethnicity-query/650547906' => Http::response([
            'list' => [],
        ]),
    ]);

    expect(fn () => AitsAzureRaceEthnicity::get('650547906'))
        ->toThrow(AitsRequestFailed::class, 'Race Ethnicity not found!');
});

test('azure race ethnicity get throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/azure/person/race-ethnicity-query/650547906' => Http::response([], 500),
    ]);

    expect(fn () => AitsAzureRaceEthnicity::get('650547906'))
        ->toThrow(AitsRequestFailed::class);
});
