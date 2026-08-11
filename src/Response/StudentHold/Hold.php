<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentHold;

use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class Hold extends Data
{
    public function __construct(
        public string $guid,
        public string $pidm,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $fromDate,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $toDate,
        public ?HoldType $holdType,
        public ?HoldOrigin $holdOrigin,
        public ?HoldReason $holdReason,
        public ?string $holdComment,
        public ?string $user,
        public ?string $releaseInd,
        public ?string $activityDate,
    ) {}
}
