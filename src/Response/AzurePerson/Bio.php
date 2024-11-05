<?php

namespace Uisits\AitsApi\Response\AzurePerson;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class Bio extends Data
{

    public function __construct(
        public string $guid,
        public string $pidm,
        public string $activityDate,
        public string $dataOrigin,
        public string $userId,
        public string $birthDate,
        public string $citzCode,
        public string $maritalStatus,
        public string $gender,
        public GenderIdentity $genderIdentity,
        public PersonalPronoun $personalPronoun,
        public string $armedForcesServiceMedal,
    ) {}
}