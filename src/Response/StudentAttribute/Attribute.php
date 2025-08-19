<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentAttribute;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\ValidPartTerm;

class Attribute extends Data
{
    public function __construct(
        public ?string $code,
        public ?string $description,
    ) {}
}
