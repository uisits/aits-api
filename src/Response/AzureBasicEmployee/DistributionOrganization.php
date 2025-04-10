<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzureBasicEmployee;

use Spatie\LaravelData\Data;

class DistributionOrganization extends Data
{
    public function __construct(
        public ?KeyValue $validChartOfAccounts,
        public ?KeyValue $validOrganization,
    ) {}
}
