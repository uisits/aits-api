<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzurePerson;

use Illuminate\Support\Collection;

class AddressCollection extends Collection
{
    public function permanentAddress(): ?Address
    {
        return $this->filter(fn (Address $address) => $address->type?->code === 'PR')->first();
    }

    public function mailingAddress(): ?Address
    {
        return $this->filter(fn (Address $address) => $address->type?->code === 'MA')->first();
    }

    public function billingAddress(): ?Address
    {
        return $this->filter(fn (Address $address) => $address->type?->code === 'BI')->first();
    }
}
