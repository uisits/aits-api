<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzureBasicEmployee;

use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class EmploymentDates extends Data
{
    public function __construct(
        #[WithCast(DateTimeInterfaceCast::class, 'M d, Y, h:i:s A')]
        #[WithTransformer(DateTimeInterfaceTransformer::class, 'M d, Y, h:i:s A')]
        public ?Carbon $firstHireDate,
        #[WithCast(DateTimeInterfaceCast::class, 'M d, Y, h:i:s A')]
        #[WithTransformer(DateTimeInterfaceTransformer::class, 'M d, Y, h:i:s A')]
        public ?Carbon $currentHireDate,
        #[WithCast(DateTimeInterfaceCast::class, 'M d, Y, h:i:s A')]
        #[WithTransformer(DateTimeInterfaceTransformer::class, 'M d, Y, h:i:s A')]
        public ?Carbon $adjustedServiceDate,
        #[WithCast(DateTimeInterfaceCast::class, 'M d, Y, h:i:s A')]
        #[WithTransformer(DateTimeInterfaceTransformer::class, 'M d, Y, h:i:s A')]
        public ?Carbon $seniorityDate,
        #[WithCast(DateTimeInterfaceCast::class, 'M d, Y, h:i:s A')]
        #[WithTransformer(DateTimeInterfaceTransformer::class, 'M d, Y, h:i:s A')]
        public ?Carbon $firstWorkDate,
    ) {}
}
