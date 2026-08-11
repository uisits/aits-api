<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentAttribute;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\ValidPartTerm;
use Uisits\AitsApi\Response\ValidTerm;

class AttributeItem extends Data
{
    public function __construct(
        public ?string $guid,
        public ?string $pidm,
        public ?ValidPartTerm $validPartTerm,
        public ?AttributeDetail $attributeDetail,
        public ?ValidTerm $termCode,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $activityDate,
        public ?Attribute $attribute,
    ) {}
}
