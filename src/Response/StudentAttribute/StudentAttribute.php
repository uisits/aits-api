<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentAttribute;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Person\Person;

class StudentAttribute extends Data
{
    public function __construct(
        public string $queryUIN,
        public string $queryCampusCode,
        public string $queryTermCode,
        public Person $person,
        /** @var AttributeCollection<int, AttributeItem> */
        public ?AttributeCollection $attribute,
    ) {}
}
