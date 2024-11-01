<?php

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
        public Level $level1,
        public StudentType $studentType,
        public College $college1,
        public Department $department1,
        public Major $major1,
        public Program $program1,
        public CatalogTerm $catalogTerm1,
        public string $expectedGradDate,
        public Degree $degree1,
    ) {}
}
