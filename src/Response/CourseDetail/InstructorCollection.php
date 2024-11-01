<?php

namespace Uisits\AitsApi\Response\CourseDetail;

use Illuminate\Support\Collection;

class InstructorCollection extends Collection
{
    public function primaryInstructor(): Instructor
    {
        return $this->where('primaryInd', 'Y')->first();
    }
}
