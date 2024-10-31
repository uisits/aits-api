<?php

namespace Uisits\AitsApi\Response\StudentHold;

use Spatie\LaravelData\Data;

class Hold extends Data
{
    public function __construct(
        public string $guid,
        public string $pidm,
        public string $fromDate,
        public string $toDate,
        public ?HoldType $holdType,
        public ?HoldOrigin $holdOrigin,
        public ?HoldReason $holdReason,
        public string $holdComment,
        public string $user,
        public string $releaseInd,
        public string $activityDate,
    ) {}
}
