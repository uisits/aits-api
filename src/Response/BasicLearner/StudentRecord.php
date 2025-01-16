<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\BasicLearner;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Campus;
use Uisits\AitsApi\Response\College;
use Uisits\AitsApi\Response\Department;

class StudentRecord extends Data
{
    public function __construct(
        public string $guid,
        public string $pidm,
        public Campus $campus,
        public EffectiveTerm $effectiveTerm,
        public Level $level,
        public StudentType $studentType,
        public College $college,
        public Department $department,
        public Major $major,
        public Program $program,
        public CatalogTerm $catalogTerm,
        public string $expectedGradDate,
        public Degree $degree,
    ) {}
}
