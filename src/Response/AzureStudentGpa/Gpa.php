<?php

namespace Uisits\AitsApi\Response\AzureStudentGpa;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\BasicLearner\Level;
use Uisits\AitsApi\Response\Person;
use Uisits\AitsApi\Response\ValidTerm;

class Gpa extends Data
{

    public function __construct(
        public string $guid,
        public string $pidm,
        public ?ValidTerm $term,
        public ?Level $level,
        public ?string $typeInd,
        public ?float $hoursAttempted,
        public ?float $hoursPassed,
        public ?float $hoursEarned,
        public ?float $hours,
        public ?float $gpa,
        public ?string $activityDate,
    ) {}
}