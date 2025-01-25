<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzurePerson;

use Spatie\LaravelData\Data;

class Phone extends Data
{
    public function __construct(
        public string $guid,
        public string $pidm,
        public string $sequenceNum,
        public ?PhoneType $type,
        public ?string $activityDate,
        public ?LinkedAddressType $linkedAddressType,
        public ?string $linkedAddressSequence,
        public ?string $primaryInd,
        public ?string $internationalAccessCode,
        public ?string $effectiveStatus,
    ) {}
}
