<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class PersonName extends Data
{

    #[Computed]
    public ?string $fullName;

    public function __construct(
        public string $lastName,
        public ?string $firstName,
        public ?string $type,
    ) {
        $this->fullName = $this->firstName . ' ' . $this->lastName;
    }
}
