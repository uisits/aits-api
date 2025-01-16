<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentEnrollment;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\LightWeightPerson;

class CourseSessionInstructor extends Data
{
    #[Computed]
    public ?string $netid = null;

    #[Computed]
    public ?string $first_name = null;

    #[Computed]
    public ?string $last_name = null;

    #[Computed]
    public ?string $uin = null;

    public function __construct(
        public LightWeightPerson $lightweightPerson,
        public string $emailAddress,
    ) {
        $this->netid = Str::of($this->emailAddress)->before('@')->value();
        $this->first_name = $this->lightweightPerson?->name?->firstName;
        $this->last_name = $this->lightweightPerson?->name?->lastName;
        $this->uin = $this->lightweightPerson?->institutionalId;
    }
}
