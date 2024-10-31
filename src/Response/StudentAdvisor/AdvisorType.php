<?php

namespace Uisits\AitsApi\Response\StudentAdvisor;

use Spatie\LaravelData\Data;

class AdvisorType extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
