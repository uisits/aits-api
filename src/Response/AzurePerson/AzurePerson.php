<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzurePerson;

use Spatie\LaravelData\Data;

class AzurePerson extends Data
{
    public function __construct(
        public ?Identity $identity,
        /** @var NameCollection<int, Name> */
        public ?NameCollection $names,
        public ?Bio $biodemo,
        /** @var AddressCollection<int, Address> */
        public ?AddressCollection $address,
        /** @var EmailCollection<int, Email> */
        public ?EmailCollection $email,
        /** @var PhoneCollection<int, Phone> */
        public ?PhoneCollection $phone,
        public ?Employee $employee,
    ) {}
}
