<?php

namespace Uisits\AitsApi\Response\BasicLearner;

use Spatie\LaravelData\Data;

class College extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
