<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AitsPersonLookup;
use Uisits\AitsApi\Response\Person\Person;

$personFixture = [
    'guid' => 'person-guid-001',
    'pidm' => '987654',
    'uin' => '650547906',
    'name' => [
        'firstName' => 'Test',
        'lastName' => 'Student',
    ],
    'netIds' => [
        ['netId' => 'tstudent', 'campusDomain' => 'uic.edu'],
    ],
    'email' => ['emailAddress' => 'tstudent@uic.edu'],
    'address' => [
        'streetLine1' => '1200 W Harrison St',
        'city' => 'Chicago',
        'state' => ['code' => 'IL', 'description' => 'Illinois'],
        'zipCode' => '60607',
    ],
    'phone' => ['areaCode' => '312', 'phoneNumber' => '5551234'],
    'title' => null,
    'employee' => null,
];

test('person lookup maps a successful response', function () use ($personFixture): void {
    Http::fake([
        'https://aits.test/person/PersonLookup/1_0/650547906' => Http::response([
            'list' => [$personFixture],
        ]),
    ]);

    $response = AitsPersonLookup::get('650547906');

    expect($response)
        ->toBeInstanceOf(Person::class)
        ->uin->toBe('650547906')
        ->guid->toBe('person-guid-001')
        ->pidm->toBe('987654');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/person/PersonLookup/1_0/650547906');
});

test('person lookup maps name with computed full_name', function () use ($personFixture): void {
    Http::fake([
        'https://aits.test/person/PersonLookup/1_0/650547906' => Http::response([
            'list' => [$personFixture],
        ]),
    ]);

    $response = AitsPersonLookup::get('650547906');

    expect($response->name->firstName)->toBe('Test')
        ->and($response->name->lastName)->toBe('Student')
        ->and($response->name->full_name)->toBe('Student, Test');
});

test('person lookup maps netids collection', function () use ($personFixture): void {
    Http::fake([
        'https://aits.test/person/PersonLookup/1_0/650547906' => Http::response([
            'list' => [$personFixture],
        ]),
    ]);

    $response = AitsPersonLookup::get('650547906');

    expect($response->netIds)->toHaveCount(1)
        ->and($response->netIds->first()->netId)->toBe('tstudent')
        ->and($response->netIds->first()->campusDomain)->toBe('uic.edu');
});

test('person lookup maps email address', function () use ($personFixture): void {
    Http::fake([
        'https://aits.test/person/PersonLookup/1_0/650547906' => Http::response([
            'list' => [$personFixture],
        ]),
    ]);

    $response = AitsPersonLookup::get('650547906');

    expect($response->email->emailAddress)->toBe('tstudent@uic.edu');
});

test('person lookup maps address fields', function () use ($personFixture): void {
    Http::fake([
        'https://aits.test/person/PersonLookup/1_0/650547906' => Http::response([
            'list' => [$personFixture],
        ]),
    ]);

    $response = AitsPersonLookup::get('650547906');

    expect($response->address->streetLine1)->toBe('1200 W Harrison St')
        ->and($response->address->city)->toBe('Chicago')
        ->and($response->address->state->code)->toBe('IL')
        ->and($response->address->zipCode)->toBe('60607');
});

test('person lookup maps phone number', function () use ($personFixture): void {
    Http::fake([
        'https://aits.test/person/PersonLookup/1_0/650547906' => Http::response([
            'list' => [$personFixture],
        ]),
    ]);

    $response = AitsPersonLookup::get('650547906');

    expect($response->phone->areaCode)->toBe('312')
        ->and($response->phone->phoneNumber)->toBe('5551234');
});

test('person lookup uses the person base url not the student base url', function () use ($personFixture): void {
    Http::fake([
        'https://aits.test/person/PersonLookup/1_0/650547906' => Http::response([
            'list' => [$personFixture],
        ]),
    ]);

    AitsPersonLookup::get('650547906');

    Http::assertSent(fn (Request $request): bool => str_starts_with($request->url(), 'https://aits.test/person/'));
});

test('person lookup throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/person/PersonLookup/1_0/650547906' => Http::response([], 500),
    ]);

    expect(fn () => AitsPersonLookup::get('650547906'))
        ->toThrow(AitsRequestFailed::class);
});

test('person lookup throws an exception when the list is empty', function (): void {
    Http::fake([
        'https://aits.test/person/PersonLookup/1_0/650547906' => Http::response([
            'list' => [],
        ]),
    ]);

    expect(fn () => AitsPersonLookup::get('650547906'))
        ->toThrow(AitsRequestFailed::class, 'Person not found!');
});
