<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentRoster;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class StudentRosterEmail extends Data
{
    #[Computed]
    public ?string $netid = null;

    public function __construct(
        public string $displayOnWeb,
        public string $status,
        public string $type,
        public string $preferred,
        public string $emailAddress,
    ) {
        $this->netid = Str::of($this->emailAddress)->before('@')->value();
    }
}
