<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;
use Uisits\AitsApi\Request\AitsCourseSummary;
use Uisits\AitsApi\Response\CourseSummary\CourseSummary;

test('course summary returns a collection of course summaries', function (): void {
    Http::fake([
        'https://aits.test/student/CourseSummary/1_0/420248' => Http::response([
            'list' => [
                [
                    'term' => '420248',
                    'crn' => '15001',
                    'subject' => ['code' => 'CS', 'description' => 'Computer Science'],
                    'number' => '101',
                ],
                [
                    'term' => '420248',
                    'crn' => '15002',
                    'subject' => ['code' => 'BIO', 'description' => 'Biology'],
                    'number' => '200',
                ],
            ],
        ]),
    ]);

    $response = AitsCourseSummary::get('420248');

    expect($response)
        ->toHaveCount(2)
        ->and($response->first())->toBeInstanceOf(CourseSummary::class)
        ->and($response->first()->term)->toBe('420248')
        ->and($response->first()->crn)->toBe('15001')
        ->and($response->first()->subject->code)->toBe('CS')
        ->and($response->first()->number)->toBe('101')
        ->and($response->last()->subject->code)->toBe('BIO');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://aits.test/student/CourseSummary/1_0/420248');
});

test('course summary returns an empty collection when list is empty', function (): void {
    Http::fake([
        'https://aits.test/student/CourseSummary/1_0/420248' => Http::response([
            'list' => [],
        ]),
    ]);

    $response = AitsCourseSummary::get('420248');

    expect($response)->toBeEmpty();
});

test('course summary maps subject code and description', function (): void {
    Http::fake([
        'https://aits.test/student/CourseSummary/1_0/420248' => Http::response([
            'list' => [
                [
                    'term' => '420248',
                    'crn' => '99001',
                    'subject' => ['code' => 'MATH', 'description' => 'Mathematics'],
                    'number' => '320',
                ],
            ],
        ]),
    ]);

    $response = AitsCourseSummary::get('420248');
    $course = $response->first();

    expect($course->subject->code)->toBe('MATH')
        ->and($course->subject->description)->toBe('Mathematics')
        ->and($course->crn)->toBe('99001')
        ->and($course->number)->toBe('320');
});

test('course summary throws an exception when the api returns a failure response', function (): void {
    Http::fake([
        'https://aits.test/student/CourseSummary/1_0/420248' => Http::response([], 500),
    ]);

    expect(fn () => AitsCourseSummary::get('420248'))
        ->toThrow(AitsRequestFailed::class);
});
