<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentHold;

use Illuminate\Support\Collection;

class HoldCollection extends Collection
{
    /**
     * Determine if a student has a specific hold.
     *
     * @param  string  $holdCode  The code for the specific hold.
     * @return bool True if the student has the specific hold, otherwise false.
     *
     * @throws \Exception
     */
    public function studentHasHold(string $holdCode): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return $this->where('holdReason.code', $holdCode)->isNotEmpty();
    }
}
