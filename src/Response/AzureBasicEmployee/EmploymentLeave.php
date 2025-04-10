<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzureBasicEmployee;

use Spatie\LaravelData\Data;

class EmploymentLeave extends Data
{
    public function __construct(
        public ?string $reason,
    ) {}
}
