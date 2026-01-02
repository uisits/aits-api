<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\CourseDetail;

use Illuminate\Support\Collection;
use Uisits\AitsApi\Response\CourseDetail\Instructor;

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
