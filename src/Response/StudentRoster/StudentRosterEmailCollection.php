<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentRoster;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StudentRosterEmailCollection extends Collection
{
    public function uisEmail(): ?StudentRosterEmail
    {
        return $this->filter(fn ($email) => Str::contains($email->emailAddress, 'uis'))
            ->first();
    }
}
