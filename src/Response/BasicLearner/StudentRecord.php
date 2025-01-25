<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\BasicLearner;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Campus;
use Uisits\AitsApi\Response\College;
use Uisits\AitsApi\Response\Department;

class StudentRecord extends Data
{
    public Collection $colleges;
    public Collection $departments;
    public Collection $majors;
    public Collection $minors;
    public Collection $concentrations;
    public Collection $programs;
    public Collection $catalogTerms;
    public Collection $degrees;

    public function __construct(
        public string $guid,
        public string $pidm,
        public ?Campus $campus,
        public ?EffectiveTerm $effectiveTerm,
        public ?Level $level1,
        public ?Level $level2,
        public ?StudentType $studentType,
        public ?College $college1,
        public ?College $college2,
        public ?Department $department1,
        public ?Department $department2,
        public ?Major $major1,
        public ?Major $major2,
        public ?Minor $minor1,
        public ?Minor $minor2,
        public ?Concentration $concentration1,
        public ?Concentration $concentration2,
        public ?Concentration $concentration3,
        public ?Concentration $concentration4,
        public ?Program $program1,
        public ?Program $program2,
        public ?CatalogTerm $catalogTerm1,
        public ?CatalogTerm $catalogTerm2,
        public ?string $expectedGradDate,
        public ?Degree $degree1,
        public ?Degree $degree2,
    ) {
        $this->colleges = collect([])->push($this->college1)->push($this->college2)->filter();
        $this->departments = collect([])->push($this->department1)->push($this->department2)->filter();
        $this->majors = collect([])->push($this->major1)->push($this->major2)->filter();
        $this->minors = collect([])->push($this->minor1)->push($this->minor2)->filter();
        $this->concentrations = collect([])
            ->push($this->concentration1)
            ->push($this->concentration2)
            ->push($this->concentration3)
            ->push($this->concentration4)
            ->filter();
        $this->programs = collect([])->push($this->program1)->push($this->program2)->filter();
        $this->catalogTerms = collect([])->push($this->catalogTerm1)->push($this->catalogTerm2)->filter();
        $this->degrees = collect([])->push($this->degree1)->push($this->degree2)->filter();
    }

    public function with(): array
    {
        return [
            'test' => 'Prasad Chinwal',
        ];
    }
}
