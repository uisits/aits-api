<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzurePerson;

use Illuminate\Support\Collection;

class EmailCollection extends Collection
{
    public function prefferedEmail(): Email
    {
        return $this->filter(fn (Email $email) => $email->preferredInd === 'Y')->first() ?? new Email();
    }
}
