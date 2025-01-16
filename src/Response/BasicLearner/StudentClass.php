<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\BasicLearner;

use Spatie\LaravelData\Data;

class StudentClass extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
