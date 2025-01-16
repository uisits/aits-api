<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzurePerson;

use Spatie\LaravelData\Data;

class Employee extends Data
{
    public function __construct(
        public string $guid,
        public string $pidm,
        public ?string $employeeStatus,
        public ?string $campusCode,
        public ?string $chartOfAccountsCode,
        public ?string $organizationCode,
        public ?string $organizationDesc,
        public ?string $employeeClass,
    ) {}
}
