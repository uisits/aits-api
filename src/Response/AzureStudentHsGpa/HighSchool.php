<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzureStudentHsGpa;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\BasicLearner\Level;
use Uisits\AitsApi\Response\ValidTerm;

class HighSchool extends Data
{
    public function __construct(
        public ?string $type,
        public ?string $highSchoolCode,
        public ?string $highSchoolName,
        public ?string $cityOrLocality,
        public ?string $stateOrProvince,
    ) {}
}
