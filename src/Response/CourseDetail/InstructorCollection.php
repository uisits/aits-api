<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\CourseDetail;

use Illuminate\Support\Collection;

class InstructorCollection extends Collection
{
    public function primaryInstructor(): ?InstructorCollection
    {
        return $this->filter(fn ($instructor) => $instructor->primaryInd === 'Y');
    }

    public function getLectureInstructors(): InstructorCollection
    {
        return $this->filter(fn ($instructor) => str_starts_with($instructor->sessionInstructorInd, 'L'));
    }

    public function getLabInstructors(): InstructorCollection
    {
        return $this->filter(fn ($instructor) => str_starts_with($instructor->sessionInstructorInd, 'B'));
    }
}
