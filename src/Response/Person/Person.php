<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\Person;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class Person extends Data
{
    public function __construct(
        public ?string $guid,
        public ?string $pidm,
        public string $uin,
        public ?Name $name,
        /** @var Collection<int, Netid> */
        public ?Collection $collection,
        public ?Address $address,
        public ?Phone $phone,
        public ?string $title,
        public ?Employee $employee,
    ) {}
}
