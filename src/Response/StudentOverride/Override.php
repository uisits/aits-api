<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentOverride;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Subject;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class Override extends Data
{
    public function __construct(
        public ?string $guid,
        public ?string $pidm,
        public ?string $termCode,
        public ?OverrideRule $rule,
        public ?Subject $subject,
        public ?string $courseNum,
        public ?string $sequenceNum,
        public ?string $crn,
        public ?string $user,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $activityDate,
    ) {}
}
