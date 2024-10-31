<?php

namespace Uisits\AitsApi\Response\CourseDetail;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class InstructorCollection extends Collection
{

    public function primaryInstructor(): Instructor
    {
        return $this->where('primaryInd', 'Y')->first();
    }
}
