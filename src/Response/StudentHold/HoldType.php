<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentHold;

use Spatie\LaravelData\Data;

class HoldType extends Data
{
    public function __construct(
        public string $code,
        public string $description,
        public ?string $registrationHoldInd,
        public ?string $displayWebHoldInd,
        public ?string $applicationHoldInd,
        public ?string $complianceHoldInd,
    ) {}
}
