<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AitsStudentAttribute;
use Uisits\AitsApi\Response\StudentAttribute\StudentAttribute;

$personFixture = [
    'guid' => 'person-guid-001',
    'pidm' => '987654',
    'uin' => '650547906',
    'name' => ['firstName' => 'Test', 'lastName' => 'Student'],
    'netIds' => [],
    'email' => null,
    'address' => null,
    'phone' => null,
    'title' => null,
    'employee' => null,
];

$attributeItemFixture = fn (string $code, string $desc) => [
    'guid' => 'attr-guid-001',
    'pidm' => '987654',
    'validPartTerm' => ['code' => '1', 'description' => 'Full Term'],
    'attributeDetail' => ['code' => $code, 'description' => $desc],
    'termCode' => ['code' => '420248', 'description' => 'Fall 2024'],
    'activityDate' => '2024-08-01',
    'attribute' => ['code' => $code, 'description' => $desc],
];

test('student attribute maps a successful response', function () use ($personFixture, $attributeItemFixture): void {
    Http::fake([
        'https://aits.test/student/StudentAttributes/1_0/650547906/400/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryCampusCode' => '400',
                    'queryTermCode' => '420248',
                    'person' => $personFixture,
                    'attribute' => [$attributeItemFixture('HONS', 'Honors')],
                ],
            ],
        ]),
    ]);

    $response = AitsStudentAttribute::get('650547906', '420248');

    expect($response)
        ->toBeInstanceOf(StudentAttribute::class)
        ->queryUIN->toBe('650547906')
        ->queryCampusCode->toBe('400')
        ->queryTermCode->toBe('420248')
        ->person->uin->toBe('650547906');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/student/StudentAttributes/1_0/650547906/400/420248');
});

test('student attribute campus code 400 is hardcoded in the url path', function () use ($personFixture, $attributeItemFixture): void {
    Http::fake([
        'https://aits.test/student/StudentAttributes/1_0/650547906/400/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryCampusCode' => '400',
                    'queryTermCode' => '420248',
                    'person' => $personFixture,
                    'attribute' => [],
                ],
            ],
        ]),
    ]);

    AitsStudentAttribute::get('650547906', '420248');

    Http::assertSent(fn (Request $request): bool => str_contains($request->url(), '/400/'));
});

test('student attribute maps attribute items collection', function () use ($personFixture, $attributeItemFixture): void {
    Http::fake([
        'https://aits.test/student/StudentAttributes/1_0/650547906/400/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryCampusCode' => '400',
                    'queryTermCode' => '420248',
                    'person' => $personFixture,
                    'attribute' => [
                        $attributeItemFixture('HONS', 'Honors'),
                        $attributeItemFixture('ATHLT', 'Athlete'),
                    ],
                ],
            ],
        ]),
    ]);

    $response = AitsStudentAttribute::get('650547906', '420248');

    expect($response->attribute)->toHaveCount(2)
        ->and($response->attribute->first()->attribute->code)->toBe('HONS')
        ->and($response->attribute->last()->attribute->code)->toBe('ATHLT');
});

test('student attribute maps activity date as a carbon instance', function () use ($personFixture, $attributeItemFixture): void {
    Http::fake([
        'https://aits.test/student/StudentAttributes/1_0/650547906/400/420248' => Http::response([
            'list' => [
                [
                    'queryUIN' => '650547906',
                    'queryCampusCode' => '400',
                    'queryTermCode' => '420248',
                    'person' => $personFixture,
                    'attribute' => [$attributeItemFixture('HONS', 'Honors')],
                ],
            ],
        ]),
    ]);

    $item = AitsStudentAttribute::get('650547906', '420248')->attribute->first();

    expect($item->activityDate)->toBeInstanceOf(\Carbon\Carbon::class)
        ->and($item->activityDate->format('Y-m-d'))->toBe('2024-08-01');
});

test('student attribute throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/student/StudentAttributes/1_0/650547906/400/420248' => Http::response([], 500),
    ]);

    expect(fn () => AitsStudentAttribute::get('650547906', '420248'))
        ->toThrow(AitsRequestFailed::class);
});

test('student attribute throws an exception when the list is empty', function (): void {
    Http::fake([
        'https://aits.test/student/StudentAttributes/1_0/650547906/400/420248' => Http::response([
            'list' => [],
        ]),
    ]);

    expect(fn () => AitsStudentAttribute::get('650547906', '420248'))
        ->toThrow(AitsRequestFailed::class, 'Student Attribute not found!');
});
