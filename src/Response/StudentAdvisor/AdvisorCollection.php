<?php

namespace Uisits\AitsApi\Response\StudentAdvisor;

use Illuminate\Support\Collection;

class AdvisorCollection extends Collection
{
    public function primaryAdvisor(): Advisor
    {
        return $this->where('primaryAdvisorInd', 'Y')->first();
    }
}
