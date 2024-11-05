<?php

namespace Uisits\AitsApi\Response\AzureStudentGpa;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Person;

class AzureStudentGpa extends Data
{

    public function __construct(
        public string $queryUIN,
        public string $queryTermCode,
        public string $queryLevelCode,
        public Person $person,
        public ?Gpa $termInstitutionalGpa,
        public ?Gpa $levelInstitutionalGpa,
        public ?Gpa $levelOverallGpa,
    ) {}
}