<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\Person;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Computed;

class Name extends Data
{
    #[Computed]
    public string $full_name;

    public function __construct(
        public string $firstName,
        public string $lastName,
    ) {
        $this->full_name = "{$this->firstName} {$this->lastName}";
    }
}
