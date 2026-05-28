<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentHold;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Computed;

class HoldType extends Data
{
    #[Computed]
    public ?bool $hasRegistrationHold;

    #[Computed]
    public ?bool $hasDisplayWebHold;

    #[Computed]
    public ?bool $hasApplicationHold;

    #[Computed]
    public ?bool $hasComplianceHold;

    public function __construct(
        public string $code,
        public string $description,
        public ?string $registrationHoldInd,
        public ?string $displayWebHoldInd,
        public ?string $applicationHoldInd,
        public ?string $complianceHoldInd,
    ) {
        $this->hasRegistrationHold = $this->registrationHoldInd === 'Y' ?? false;
        $this->hasDisplayWebHold = $this->displayWebHoldInd === 'Y' ?? false;
        $this->hasApplicationHold = $this->applicationHoldInd === 'Y' ?? false;
        $this->hasComplianceHold = $this->complianceHoldInd === 'Y' ?? false;
    }
}
