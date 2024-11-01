<?php

namespace Uisits\AitsApi\Response\StudentAttribute;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\ValidPartTerm;

class Attribute extends Data
{
    public function __construct(
        public string $guid,
        public string $pidm,
        public ValidPartTerm $termCode,
        public AttributeDetail $attribute,
        public string $activityDate,
    ) {}
}
