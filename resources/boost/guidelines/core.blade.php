## UIS AITS API

- `uisits/aits-api` provides access to University of Illinois Banner and Azure APIs for student, course, person, and employee data.
- All request classes use static methods — no instantiation needed (e.g. `AitsStudentEnrollment::get($uin, $term)`).
- All responses are typed Spatie `LaravelData` objects; access properties directly. Nullable properties use the null-safe operator `?->`.
- Wrap all calls in a try/catch for `Uisits\AitsApi\Exceptions\AitsRequestFailed` — code `404` means not found, `500` means a network or server error.

### Available Request Classes

| Class | Method | Returns | Source |
|-------|--------|---------|--------|
| `AitsStudentEnrollment` | `get(uin, term)` | `StudentEnrollment` | Banner |
| `AitsStudentRoster` | `get(term, crn)` | `Collection<StudentRoster>` | Banner |
| `AitsStudentHold` | `get(uin)` / `put(uin)` | `StudentHold` / `Collection<StudentHold>` | Banner |
| `AitsStudentAttribute` | `get(uin, term)` | `StudentAttribute` | Banner |
| `AitsStudentOverride` | `get(uin, term)` / `update(...)` / `delete(...)` | `StudentOverride` / `bool` | Banner |
| `AitsStudentAdvisor` | `get(uin, term)` | `StudentAdvisor` | Banner |
| `AitsBasicLearner` | `get(uin, term)` | `BasicLearner` | Banner |
| `AitsCourseDetail` | `get(term, crn)` | `Collection<CourseDetail>` | Banner |
| `AitsCourseSummary` | `get(term)` | `Collection<CourseSummary>` | Banner |
| `AitsPersonLookup` | `get(uin)` | `Person` | Banner |
| `AitsAzureStudentGpa` | `get(uin, termCode, level)` | `AzureStudentGpa` | Azure |
| `AitsAzureStudentHighSchoolGpa` | `get(uin)` | `AzureStudentHsGpa` | Azure |
| `AitsAzurePersonLookup` | `get(uin)` | `AzurePerson` | Azure |
| `AitsAzureRaceEthnicity` | `get(uin)` | `RaceEthnicity` | Azure |
| `AitsAzureBasicEmployee` | `get(uin)` | `AzureEmployee` | Azure |
| `AitsAzureCourseDetail` | `get(term, crn)` | `Collection<CourseDetail>` | Azure |

### CourseDetail Instructor Helpers

`$course->instructor` is an `InstructorCollection` with helpers: `primaryInstructor()`, `getLectureInstructors()`, `getLabInstructors()`.
