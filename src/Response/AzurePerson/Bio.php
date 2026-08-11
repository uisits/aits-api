<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzurePerson;

use Spatie\LaravelData\Data;

class Bio extends Data
{
    public function __construct(
        public string $guid,
        public string $pidm,
        public ?string $activityDate,
        public ?string $dataOrigin,
        public ?string $userId,
        public ?string $birthDate,
        public ?string $citzCode,
        public ?string $maritalStatus,
        public ?string $gender,
        public ?string $confidentialInd,
        public ?GenderIdentity $genderIdentity,
        public ?PersonalPronoun $personalPronoun,
        public ?string $armedForcesServiceMedal,
    ) {}
}
