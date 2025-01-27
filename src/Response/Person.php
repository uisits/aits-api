<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class Person extends Data
{
    #[Computed]
    public ?string $fullName;

    public function __construct(
        public string $guid,
        public string $pidm,
        public string $uin,
        public ?string $lastName,
        public ?string $firstName,
    ) {
        $this->fullName = $this->lastName . ', ' . $this->firstName;
    }
}
