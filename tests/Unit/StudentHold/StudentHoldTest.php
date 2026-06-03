<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AitsStudentHold;
use Uisits\AitsApi\Response\StudentHold\StudentHold;

$personFixture = [
    'guid' => 'person-guid-001',
    'pidm' => '987654',
    'uin' => '650547906',
    'lastName' => 'Student',
    'firstName' => 'Test',
];

$holdFixture = fn (string $holdCode) => [
    'guid' => 'hold-guid-001',
    'pidm' => '987654',
    'fromDate' => '2024-01-01',
    'toDate' => '2024-12-31',
    'holdType' => ['code' => 'HO', 'description' => 'Hold'],
    'holdOrigin' => ['code' => 'UNIV', 'description' => 'University'],
    'holdReason' => ['code' => $holdCode, 'description' => 'Reason'],
    'holdComment' => 'Please resolve this hold.',
    'user' => 'BANNER',
    'releaseInd' => 'Y',
    'activityDate' => '2024-01-01',
];

test('student hold get maps a successful response', function () use ($personFixture, $holdFixture): void {
    Http::fake([
        'https://aits.test/student/StudentHolds/1_0/650547906' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'person' => $personFixture,
                    'hold' => [$holdFixture('LIB')],
                ],
            ],
        ]),
    ]);

    $response = AitsStudentHold::get('650547906');

    expect($response)
        ->toBeInstanceOf(StudentHold::class)
        ->queryUIN->toBe('650547906')
        ->person->uin->toBe('650547906');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/student/StudentHolds/1_0/650547906'
        && $request->method() === 'GET');
});

test('student hold get maps hold dates as carbon instances', function () use ($personFixture, $holdFixture): void {
    Http::fake([
        'https://aits.test/student/StudentHolds/1_0/650547906' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'person' => $personFixture,
                    'hold' => [$holdFixture('LIB')],
                ],
            ],
        ]),
    ]);

    $hold = AitsStudentHold::get('650547906')->hold->first();

    expect($hold->fromDate)->toBeInstanceOf(\Carbon\Carbon::class)
        ->and($hold->fromDate->format('Y-m-d'))->toBe('2024-01-01')
        ->and($hold->toDate)->toBeInstanceOf(\Carbon\Carbon::class)
        ->and($hold->toDate->format('Y-m-d'))->toBe('2024-12-31');
});

test('student hold get maps all hold fields', function () use ($personFixture, $holdFixture): void {
    Http::fake([
        'https://aits.test/student/StudentHolds/1_0/650547906' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'person' => $personFixture,
                    'hold' => [$holdFixture('LIB')],
                ],
            ],
        ]),
    ]);

    $hold = AitsStudentHold::get('650547906')->hold->first();

    expect($hold->guid)->toBe('hold-guid-001')
        ->and($hold->holdType->code)->toBe('HO')
        ->and($hold->holdOrigin->code)->toBe('UNIV')
        ->and($hold->holdReason->code)->toBe('LIB')
        ->and($hold->holdComment)->toBe('Please resolve this hold.')
        ->and($hold->releaseInd)->toBe('Y');
});

test('hold collection studentHasHold returns true when hold code exists', function () use ($personFixture, $holdFixture): void {
    Http::fake([
        'https://aits.test/student/StudentHolds/1_0/650547906' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'person' => $personFixture,
                    'hold' => [$holdFixture('LIB'), $holdFixture('FIN')],
                ],
            ],
        ]),
    ]);

    $holds = AitsStudentHold::get('650547906')->hold;

    expect($holds->studentHasHold('LIB'))->toBeTrue()
        ->and($holds->studentHasHold('FIN'))->toBeTrue()
        ->and($holds->studentHasHold('REG'))->toBeFalse();
});

test('student hold put uses the put http method and returns a collection', function (): void {
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

test('student hold get throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/student/StudentHolds/1_0/650547906' => Http::response([], 500),
    ]);

    expect(fn () => AitsStudentHold::get('650547906'))
        ->toThrow(AitsRequestFailed::class);
});

test('student hold get throws an exception when the list is empty', function (): void {
    Http::fake([
        'https://aits.test/student/StudentHolds/1_0/650547906' => Http::response([
            'list' => [],
        ]),
    ]);

    expect(fn () => AitsStudentHold::get('650547906'))
        ->toThrow(AitsRequestFailed::class, 'Student Holds not found!');
});

test('student hold put throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/student/StudentHolds/1_0/650547906' => Http::response([], 500),
    ]);

    expect(fn () => AitsStudentHold::put('650547906'))
        ->toThrow(AitsRequestFailed::class);
});
