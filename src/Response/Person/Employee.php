<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\Person;

use Spatie\LaravelData\Data;

class Employee extends Data
{
    public function __construct(
        public string $chartOfAccountsCode,
        public ?string $organizationCode,
        public ?string $organizationDesc,
        public string $employeeClass,
    ) {}
}
