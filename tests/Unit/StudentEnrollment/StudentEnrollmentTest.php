<?php

declare(strict_types=1);

test('it throws exception for missing term', function (): void {
    \Uisits\AitsApi\Request\AitsStudentEnrollment::get(uin: '1234');
})->throws(ArgumentCountError::class);

test('it throws exception for missing uin', function (): void {
    \Uisits\AitsApi\Request\AitsStudentEnrollment::get(term: '1234');
})->throws(ArgumentCountError::class);

test('it throws exception for missing uin and term', function (): void {
    \Uisits\AitsApi\Request\AitsStudentEnrollment::get();
})->throws(ArgumentCountError::class);

test('it gives collection response on success', function () {
    $mock = Mockery::mock(\Uisits\AitsApi\Request\AitsStudentEnrollment::class);
    $mock = $mock->shouldReceive('get')
        ->with('650547906', '420248')
        ->andReturn(Mockery::mock(\Uisits\AitsApi\Response\StudentEnrollment\StudentEnrollment::class));

    expect($mock)->dd();
});
