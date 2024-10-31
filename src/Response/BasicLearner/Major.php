<?php

namespace Uisits\AitsApi\Response\BasicLearner;

use Spatie\LaravelData\Data;

class Major extends Data
{
    public function __construct(
        public string $code,
        public string $description,
        public string $majorInd,
    ) {}
}
