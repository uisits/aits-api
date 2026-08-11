<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzurePerson;

use Spatie\LaravelData\Data;

class Email extends Data
{
    public function __construct(
        public string $guid,
        public string $pidm,
        public ?string $activityDate,
        public ?string $emailCode,
        public ?string $emailAddress,
        public ?string $statusInd,
        public ?string $preferredInd,
        public ?string $displayWebInd,
        public ?string $dataOrigin,
        public ?string $effectiveStatus,
    ) {}
}
