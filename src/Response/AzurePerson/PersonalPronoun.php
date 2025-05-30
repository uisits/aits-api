<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzurePerson;

use Spatie\LaravelData\Data;

class PersonalPronoun extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
