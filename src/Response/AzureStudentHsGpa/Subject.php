<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzureStudentHsGpa;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\BasicLearner\Level;
use Uisits\AitsApi\Response\ValidTerm;

class Subject extends Data
{
    public function __construct(
        public ?string $subjectCode,
    ) {}
}
