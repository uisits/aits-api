<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzurePerson;

use Spatie\LaravelData\Data;

class Address extends Data
{
    public function __construct(
        public string $guid,
        public string $pidm,
        public string $fromDate,
        public ?string $toDate,
        public ?string $activityDate,
        public ?string $statusInd,
        public AddressType $addressType,
        public ?string $sequenceNum,
        public ?string $streetLine1,
        public ?string $streetLine2,
        public ?string $streetLine3,
        public ?string $city,
        public ?State $state,
        public ?string $zipCode,
        public ?County $county,
        public ?Nation $nation,
        public ?string $effectiveStatus,
        /** @var PhoneCollection<int, Phone> */
        public ?PhoneCollection $phoneCollection,
    ) {}
}
