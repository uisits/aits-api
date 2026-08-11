<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AitsStudentRoster;
use Uisits\AitsApi\Response\StudentRoster\StudentRoster;

$rosterEntryFixture = fn (string $uin, string $firstName, string $lastName) => [
    'lightweightPerson' => [
        'name' => [
            'lastName' => $lastName,
            'firstName' => $firstName,
            'type' => 'LEGAL',
        ],
        'institutionalId' => $uin,
    ],
    'email' => [
        [
            'displayOnWeb' => 'Y',
            'status' => 'A',
            'type' => 'UINSTI',
            'preferred' => 'Y',
            'emailAddress' => strtolower($firstName).'.'.strtolower($lastName).'@uic.edu',
        ],
    ],
    'courseReferenceNumber' => '15181',
    'validTerm' => ['code' => '420248', 'description' => 'Fall 2024'],
    'validRegistrationStatus' => ['code' => 'RW', 'description' => 'Registered Web'],
    'validCourseRegistrationLevel' => ['code' => 'UG', 'description' => 'Undergraduate'],
];

test('student roster get returns a collection of student roster entries', function () use ($rosterEntryFixture): void {
    Http::fake([
        'https://aits.test/student/StudentRoster/1_0/420248/15181' => Http::response([
            'list' => [
                $rosterEntryFixture('650547906', 'Alice', 'Smith'),
                $rosterEntryFixture('660123456', 'Bob', 'Jones'),
            ],
        ]),
    ]);

    $response = AitsStudentRoster::get('420248', '15181');

    expect($response)
        ->toHaveCount(2)
        ->and($response->first())->toBeInstanceOf(StudentRoster::class)
        ->and($response->first()->lightweightPerson->institutionalId)->toBe('650547906')
        ->and($response->first()->courseReferenceNumber)->toBe('15181')
        ->and($response->first()->validTerm->code)->toBe('420248')
        ->and($response->first()->validRegistrationStatus->code)->toBe('RW');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/student/StudentRoster/1_0/420248/15181');
});

test('student roster get returns an empty collection when no students are enrolled', function (): void {
    Http::fake([
        'https://aits.test/student/StudentRoster/1_0/420248/15181' => Http::response([
            'list' => [],
        ]),
    ]);

    $response = AitsStudentRoster::get('420248', '15181');

    expect($response)->toBeEmpty();
});

test('student roster maps lightweight person name fields', function () use ($rosterEntryFixture): void {
    Http::fake([
        'https://aits.test/student/StudentRoster/1_0/420248/15181' => Http::response([
            'list' => [$rosterEntryFixture('650547906', 'Alice', 'Smith')],
        ]),
    ]);

    $student = AitsStudentRoster::get('420248', '15181')->first();

    expect($student->lightweightPerson->name->firstName)->toBe('Alice')
        ->and($student->lightweightPerson->name->lastName)->toBe('Smith')
        ->and($student->lightweightPerson->name->type)->toBe('LEGAL');
});

test('student roster email has computed netid from email address', function () use ($rosterEntryFixture): void {
    Http::fake([
        'https://aits.test/student/StudentRoster/1_0/420248/15181' => Http::response([
            'list' => [$rosterEntryFixture('650547906', 'Alice', 'Smith')],
        ]),
    ]);

    $email = AitsStudentRoster::get('420248', '15181')->first()->email->first();

    expect($email->emailAddress)->toBe('alice.smith@uic.edu')
        ->and($email->netid)->toBe('alice.smith')
        ->and($email->preferred)->toBe('Y');
});

test('student roster maps course registration level', function () use ($rosterEntryFixture): void {
    Http::fake([
        'https://aits.test/student/StudentRoster/1_0/420248/15181' => Http::response([
            'list' => [$rosterEntryFixture('650547906', 'Alice', 'Smith')],
        ]),
    ]);

    $student = AitsStudentRoster::get('420248', '15181')->first();

    expect($student->validCourseRegistrationLevel->code)->toBe('UG')
        ->and($student->validCourseRegistrationLevel->description)->toBe('Undergraduate');
});

test('student roster throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/student/StudentRoster/1_0/420248/15181' => Http::response([], 500),
    ]);

    expect(fn () => AitsStudentRoster::get('420248', '15181'))
        ->toThrow(AitsRequestFailed::class);
});
