<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentAttribute;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\ValidPartTerm;

class Attribute extends Data
{
    public function __construct(
        public ?string $guid,
        public ?string $pidm,
        public ?ValidPartTerm $validPartTerm,
        public ?AttributeDetail $attributeDetail,
        public ?string $activityDate,
    ) {}
}
