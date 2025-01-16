<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentAdvisor;

use Spatie\LaravelData\Data;

class AdvisorTerm extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
