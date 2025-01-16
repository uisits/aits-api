<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\RaceEthnicity;

use Spatie\LaravelData\Data;

class RaceEthnicity extends Data
{
    public function __construct(
        public string $pidm,
        public string $uin,
        public string $oldEthnicity,
        public ValidEthnicity $validEthnicity,
        /** @var RaceCollection<int, Race> */
        public RaceCollection $race,
        public RaceEthnicityConfirmation $raceEthnicityConfirmation,
    ) {}
}
