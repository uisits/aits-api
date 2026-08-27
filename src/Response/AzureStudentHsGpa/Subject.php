<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzureStudentHsGpa;

use Spatie\LaravelData\Data;

class Subject extends Data
{
    public function __construct(
        public ?string $subjectCode,
        public ?string $subjectYears,
    ) {}
}
