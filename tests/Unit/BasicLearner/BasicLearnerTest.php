<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AitsBasicLearner;
use Uisits\AitsApi\Response\BasicLearner\BasicLearner;

$personFixture = [
    'guid' => 'person-guid-001',
    'pidm' => '987654',
    'uin' => '650547906',
    'lastName' => 'Student',
    'firstName' => 'Test',
];

$studentRecordFixture = [
    'guid' => 'record-guid-001',
    'pidm' => '987654',
    'campus' => ['code' => 'UIC', 'description' => 'Chicago'],
    'level1' => ['code' => 'UG', 'description' => 'Undergraduate'],
    'college1' => ['code' => 'ENG', 'description' => 'College of Engineering'],
    'major1' => ['code' => 'CS', 'description' => 'Computer Science'],
    'degree1' => ['code' => 'BS', 'description' => 'Bachelor of Science'],
];

test('basic learner maps a successful response', function () use ($personFixture): void {
    Http::fake([
        'https://aits.test/student/BasicLearner/1_0/650547906/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryTermCode' => '420248',
                    'person' => $personFixture,
                    'studentRecord' => null,
                    'studentClass' => ['code' => 'SR', 'description' => 'Senior'],
                ],
            ],
        ]),
    ]);

    $response = AitsBasicLearner::get('650547906', '420248');

    expect($response)
        ->toBeInstanceOf(BasicLearner::class)
        ->queryUIN->toBe('650547906')
        ->queryTermCode->toBe('420248')
        ->person->uin->toBe('650547906')
        ->studentClass->code->toBe('SR')
        ->studentClass->description->toBe('Senior');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/student/BasicLearner/1_0/650547906/420248');
});

test('basic learner maps student record with academic program fields', function () use ($personFixture, $studentRecordFixture): void {
    Http::fake([
        'https://aits.test/student/BasicLearner/1_0/650547906/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryTermCode' => '420248',
                    'person' => $personFixture,
                    'studentRecord' => $studentRecordFixture,
                    'studentClass' => null,
                ],
            ],
        ]),
    ]);

    $response = AitsBasicLearner::get('650547906', '420248');

    expect($response->studentRecord->campus->code)->toBe('UIC')
        ->and($response->studentRecord->level1->code)->toBe('UG')
        ->and($response->studentRecord->college1->code)->toBe('ENG')
        ->and($response->studentRecord->major1->code)->toBe('CS')
        ->and($response->studentRecord->degree1->code)->toBe('BS')
        ->and($response->studentClass)->toBeNull();
});

test('basic learner student record aggregated collections include non-null items', function () use ($personFixture): void {
    Http::fake([
        'https://aits.test/student/BasicLearner/1_0/650547906/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryTermCode' => '420248',
                    'person' => $personFixture,
                    'studentRecord' => [
                        'guid' => 'record-guid-001',
                        'pidm' => '987654',
                        'college1' => ['code' => 'ENG', 'description' => 'College of Engineering'],
                        'college2' => ['code' => 'LAS', 'description' => 'Liberal Arts and Sciences'],
                        'major1' => ['code' => 'CS', 'description' => 'Computer Science'],
                        'major2' => null,
                        'degree1' => ['code' => 'BS', 'description' => 'Bachelor of Science'],
                        'degree2' => null,
                        'level1' => ['code' => 'UG', 'description' => 'Undergraduate'],
                        'level2' => null,
                    ],
                    'studentClass' => null,
                ],
            ],
        ]),
    ]);

    $record = AitsBasicLearner::get('650547906', '420248')->studentRecord;

    expect($record->colleges)->toHaveCount(2)
        ->and($record->majors)->toHaveCount(1)
        ->and($record->degrees)->toHaveCount(1)
        ->and($record->levels)->toHaveCount(1)
        ->and($record->minors)->toBeEmpty()
        ->and($record->concentrations)->toBeEmpty();
});

test('basic learner person has computed full name', function () use ($personFixture): void {
    Http::fake([
        'https://aits.test/student/BasicLearner/1_0/650547906/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryTermCode' => '420248',
                    'person' => $personFixture,
                    'studentRecord' => null,
                    'studentClass' => null,
                ],
            ],
        ]),
    ]);

    $response = AitsBasicLearner::get('650547906', '420248');

    expect($response->person->fullName)->toBe('Student, Test');
});

test('basic learner throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/student/BasicLearner/1_0/650547906/420248' => Http::response([], 500),
    ]);

    expect(fn () => AitsBasicLearner::get('650547906', '420248'))
        ->toThrow(AitsRequestFailed::class);
});

test('basic learner throws an exception when the list is empty', function (): void {
    Http::fake([
        'https://aits.test/student/BasicLearner/1_0/650547906/420248' => Http::response([
            'list' => [],
        ]),
    ]);

    expect(fn () => AitsBasicLearner::get('650547906', '420248'))
        ->toThrow(AitsRequestFailed::class, 'Basic Learner not found!');
});
