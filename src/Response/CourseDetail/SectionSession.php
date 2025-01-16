<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\CourseDetail;

use Spatie\LaravelData\Data;

class SectionSession extends Data
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}
}
