<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\RaceEthnicity;

use Spatie\LaravelData\Data;

class Race extends Data
{
    public function __construct(
        public ValidRace $validRace,
        public string $userId,
        public string $dataOrigin,
        public string $activityDatetime,
        public ValidRegulatoryRace $validRegulatoryRace,
    ) {}
}
