<?php

namespace Uisits\AitsApi\Response\StudentRoster;

use Spatie\LaravelData\Data;

class StudentRosterEmail extends Data
{
    public function __construct(
        public string $displayOnWeb,
        public string $status,
        public string $type,
        public string $preferred,
        public string $emailAddress,
    ) {}
}
