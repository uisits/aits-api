<?php

namespace Uisits\AitsApi\Response\StudentAttribute;

use Spatie\LaravelData\Data;

class AttributeDetail extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}