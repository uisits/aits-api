<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\CourseDetail;

use Illuminate\Support\Collection;

class InstructorCollection extends Collection
{
    public function primaryInstructor(): ?Instructor
    {
        return $this->where('primaryInd', 'Y')
            ->first();
    }

    public function getLectureInstructors()
    {
        return $this->where('sessionInstructorInd', 'Y');
    }
}
