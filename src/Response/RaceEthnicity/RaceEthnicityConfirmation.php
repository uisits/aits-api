<?php

namespace Uisits\AitsApi\Response\RaceEthnicity;

use Carbon\CarbonImmutable;
use DateTime;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class RaceEthnicityConfirmation extends Data
{
    public function __construct(
        public string $confirmedIndicator,
        #[WithCast(DateTimeInterfaceCast::class, DATE_ATOM)]
        public $confirmedDate,
    ) {}
}