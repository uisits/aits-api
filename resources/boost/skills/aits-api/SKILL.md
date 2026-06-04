---
name: uis-aits-api
description: Access University of Illinois Student Information System (Banner) and Azure API data including student enrollment, holds, overrides, advisors, GPA, course details, person lookups, and employee information using uisits/aits-api.
---

# UIS AITS API

## When to use this skill

Use this skill when the user needs to retrieve or manipulate student, course, person, or employee data from the University of Illinois Banner or Azure APIs using `uisits/aits-api`. This includes fetching student enrollment records, holds, overrides, advisors, course rosters, course details, person lookups, GPA data, race/ethnicity, and employee information. All request classes use static methods — no instantiation needed. All responses are typed Spatie LaravelData objects with direct property access.

## Installation & Configuration

### Install

```bash
composer require uisits/aits-api
php artisan vendor:publish --tag=aits-api
```

### Environment variables

```env
# Banner API (student/course data)
AITS_BASE_URL=https://webservices-test.admin.uillinois.edu/studentWS/data/edu.uis.its.apps/

# Banner Person API
AITS_PERSON_BASE_URL=https://webservices-test.admin.uillinois.edu/personWS/data/edu.uis.its.apps/

# Azure API
AITS_AZURE_PORTAL_KEY=your-subscription-key
AITS_AZURE_BASE_URL=https://api.apps.uillinois.edu/

# Optional: Proxy
AITS_PROXY_HOST=x.x.x.x
AITS_PROXY_PORT=xxxx
AITS_PROXY_USERNAME=proxyuser
AITS_PROXY_PASSWORD=proxypassword
```

### HTTP macros

The service provider registers three HTTP macros:

| Macro | Auth | Used By |
|-------|------|---------|
| `Http::aits()` | None (network/proxy) | Banner student/course requests |
| `Http::aitsPerson()` | None | Banner person lookup |
| `Http::aitsAzure()` | `Ocp-Apim-Subscription-Key` header | All Azure requests |

## Error Handling

All request methods throw `Uisits\AitsApi\Exceptions\AitsRequestFailed` on failure:

```php
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $enrollment = AitsStudentEnrollment::get('123456789', '120241');
} catch (AitsRequestFailed $e) {
    // $e->getMessage() — human-readable message
    // $e->getCode()    — HTTP status code (404, 500, etc.)
    // $e->getPrevious() — original exception
    logger()->error('AITS error', ['message' => $e->getMessage(), 'code' => $e->getCode()]);
}
```

| Code | Meaning |
|------|---------|
| `404` | Record not found in Banner/Azure |
| `500` | API request failed (network, auth, server error) |

## Student Enrollment

Retrieve a student's complete enrollment record for a given term, including registered courses.

```php
use Uisits\AitsApi\Request\AitsStudentEnrollment;

$enrollment = AitsStudentEnrollment::get('123456789', '120241');

// Student identity
echo $enrollment->lightweightPerson->institutionalId;
echo $enrollment->lightweightPerson->name->firstName;
echo $enrollment->lightweightPerson->name->lastName;

// Enrollment status
echo $enrollment->validEnrollmentStatus->code;    // 'EL'
echo $enrollment->validEnrollmentStatus->description; // 'Eligible'

// Term
echo $enrollment->validTerm->code;                // '120241'

// Registered courses
foreach ($enrollment->courseRegistration as $course) {
    echo $course->courseSection->courseReferenceNumber; // '12345'
    echo $course->validRegistrationStatus->code;         // 'RW'
    echo $course->validGradingMode->code;                // 'Standard'
    echo $course->validCourseRegistrationLevel->code;    // 'UG'
}
```

**Returns:** `StudentEnrollment`

**Underlying endpoint:** `GET /StudentEnrollment/1_0/{uin}/{term}`

## Student Roster

Retrieve the full roster of students enrolled in a specific course section.

```php
use Uisits\AitsApi\Request\AitsStudentRoster;

$roster = AitsStudentRoster::get('120241', '12345');

echo $roster->count(); // number of enrolled students

foreach ($roster as $student) {
    echo $student->lightweightPerson->institutionalId;  // UIN
    echo $student->lightweightPerson->name->firstName;
    echo $student->courseReferenceNumber;
    echo $student->validRegistrationStatus->code;

    foreach ($student->email as $email) {
        echo $email->emailAddress;
        echo $email->preferredInd;  // 'Y' or 'N'
    }
}
```

**Returns:** `Collection<StudentRoster>` (may be empty)

**Underlying endpoint:** `GET /StudentRoster/1_0/{term}/{crn}`

## Student Holds

Retrieve or release holds for a student.

### Get holds

```php
use Uisits\AitsApi\Request\AitsStudentHold;

$holdRecord = AitsStudentHold::get('123456789');

echo $holdRecord->queryUIN;
echo $holdRecord->person->name->firstName;

foreach ($holdRecord->hold as $hold) {
    echo $hold->guid;
    echo $hold->fromDate?->format('Y-m-d');  // Carbon date
    echo $hold->toDate?->format('Y-m-d');
    echo $hold->holdType?->code;
    echo $hold->holdType?->description;
    echo $hold->holdOrigin?->code;
    echo $hold->holdReason?->description;
    echo $hold->holdComment;
    echo $hold->releaseInd;  // 'Y' if releaseable
}
```

**Returns:** `StudentHold`

**Underlying endpoint:** `GET /StudentHolds/1_0/{uin}`

### Release holds

```php
$updatedHolds = AitsStudentHold::put('123456789');
// Returns Collection<StudentHold> (may be empty after release)
```

**Returns:** `Collection<StudentHold>`

**Underlying endpoint:** `PUT /StudentHolds/1_0/{uin}`

## Student Attributes

Retrieve student attribute codes for a student/term combination. Campus code `400` (UIS) is hardcoded.

```php
use Uisits\AitsApi\Request\AitsStudentAttribute;

$attributes = AitsStudentAttribute::get('123456789', '120241');

echo $attributes->queryUIN;
echo $attributes->queryCampusCode;  // '400'
echo $attributes->queryTermCode;

foreach ($attributes->attribute as $attr) {
    echo $attr->code;
    echo $attr->description;
}
```

**Returns:** `StudentAttribute`

**Underlying endpoint:** `GET /StudentAttributes/1_0/{uin}/400/{term}`

## Student Overrides

Retrieve, create, or delete registration overrides (prerequisite/capacity waivers).

### Get overrides

```php
use Uisits\AitsApi\Request\AitsStudentOverride;

$overrides = AitsStudentOverride::get('123456789', '120241');

echo $overrides->queryUIN;
echo $overrides->queryTermCode;

foreach ($overrides->overrides as $override) {
    echo $override->crn;
    echo $override->rule?->code;
    echo $override->rule?->description;
}
```

**Returns:** `StudentOverride`

**Underlying endpoint:** `GET /StudentRegistrationOverrides/1_0/{uin}/{term}`

### Grant an override

```php
$success = AitsStudentOverride::update(
    term: '120241',
    pidm: '99887766',
    crn: '12345',
    overrideCode: 'PREQ',
    overrideDescription: 'Prerequisite Override'
);

if ($success) {
    // override granted
}
```

**Parameters:** `term`, `pidm` (Banner internal person ID), `crn`, `overrideCode`, `overrideDescription`

**Returns:** `bool`

**Underlying endpoint:** `POST /StudentRegistrationOverride/1_0/`

### Remove an override

```php
$deleted = AitsStudentOverride::delete(
    term: '120241',
    pidm: '99887766',
    crn: '12345',
    overrideCode: 'PREQ',
    overrideDescription: 'Prerequisite Override'
);
```

Same parameters as `update`. Returns `bool`.

**Underlying endpoint:** `POST /StudentRegistrationOverride/1_0/?delete=true`

## Student Advisors

Retrieve a student's academic advisor(s) for a given term.

```php
use Uisits\AitsApi\Request\AitsStudentAdvisor;

$advisorRecord = AitsStudentAdvisor::get('123456789', '120241');

echo $advisorRecord->queryUIN;
echo $advisorRecord->queryTermCode;
echo $advisorRecord->person->name->firstName;

foreach ($advisorRecord->advisors as $advisor) {
    // Computed convenience properties
    echo $advisor->fullName;           // 'John Smith'
    echo $advisor->firstName;
    echo $advisor->lastName;
    echo $advisor->uin;                // advisor's UIN
    echo $advisor->pidm;               // advisor's PIDM
    echo $advisor->primaryAdvisorInd;  // 'Y'
    echo $advisor->advisorType?->code;
    echo $advisor->advisorTerm?->code;
}
```

**Returns:** `StudentAdvisor`

**Underlying endpoint:** `GET /StudentAdvisors/1_0/{uin}/{term}`

## Basic Learner (Academic Program)

Retrieve a student's academic program record for a given term (major, college, degree, level, etc.).

```php
use Uisits\AitsApi\Request\AitsBasicLearner;

$learner = AitsBasicLearner::get('123456789', '120241');

echo $learner->queryUIN;
echo $learner->queryTermCode;
echo $learner->person->name->firstName;
echo $learner->studentClass?->code;     // 'FR', 'JR', etc.
echo $learner->studentClass?->description;

// StudentRecord contains aggregated collections
$record = $learner->studentRecord;

echo $record->campus?->code;
echo $record->level1?->code;  // 'UG', 'GR', etc.

foreach ($record->majors as $major) {
    echo $major->code;
    echo $major->description;
}

foreach ($record->colleges as $college) {
    echo $college->code;
}

foreach ($record->degrees as $degree) {
    echo $degree->code;
    echo $degree->description;
}

foreach ($record->minors as $minor) {
    echo $minor->code;
}

foreach ($record->concentrations as $concentration) {
    echo $concentration->code;
}
```

**Returns:** `BasicLearner`

**Underlying endpoint:** `GET /BasicLearner/1_0/{uin}/{term}`

## Course Detail

Retrieve detailed course section information including instructors, meeting times, enrollment counts, and cross-list data.

```php
use Uisits\AitsApi\Request\AitsCourseDetail;

$details = AitsCourseDetail::get('120241', '12345');

foreach ($details as $course) {
    echo $course->term;                             // '120241'
    echo $course->crn;                              // '12345'
    echo $course->subject->code;                    // 'CS'
    echo $course->subject->description;             // 'Computer Science'
    echo $course->number;                           // '101'
    echo $course->title;                            // 'Intro to Programming'
    echo $course->sectionNumber;                    // '001'
    echo $course->scheduleType->code;               // 'LEC', 'LAB', etc.
    echo $course->sectionEnrollment;                // 25
    echo $course->sectionMaxEnrollment;             // 30
    echo $course->sectionAvailableSeats;            // 5
    echo $course->sectionWaitCount;
    echo $course->sectionMeetingDays;               // 'MWF'
    echo $course->sectionMeetingHours;              // '0900-0950'
    echo $course->sectionBuildingDescription;
    echo $course->sectionRoomNumber;

    // Cross-listing
    echo $course->crossListGroupID;
    echo $course->crossListSectionEnrollment;

    // Instructors (see InstructorCollection helpers below)
    $primary = $course->instructor->primaryInstructor()->first();
    echo $primary?->firstName . ' ' . $primary?->lastName;
}
```

**Returns:** `Collection<CourseDetail>` (multiple for cross-listed sections)

**Underlying endpoint:** `GET /CourseDetail/1_0/{term}/{crn}`

## Course Summary

Retrieve a summary listing of all course sections offered in a given term.

```php
use Uisits\AitsApi\Request\AitsCourseSummary;

$courses = AitsCourseSummary::get('120241');

echo $courses->count();  // total sections in the term

foreach ($courses as $course) {
    echo $course->term;              // '120241'
    echo $course->crn;               // course reference number
    echo $course->subject->code;     // 'CS'
    echo $course->subject->description;
    echo $course->number;            // '101'
}
```

**Returns:** `Collection<CourseSummary>` (may be empty)

**Underlying endpoint:** `GET /CourseSummary/1_0/{term}`

## Person Lookup (Banner)

Retrieve full person identity and contact information from the Banner person service.

```php
use Uisits\AitsApi\Request\AitsPersonLookup;

$person = AitsPersonLookup::get('123456789');

echo $person->guid;
echo $person->pidm;
echo $person->uin;
echo $person->name?->firstName;
echo $person->name?->lastName;
echo $person->name?->full_name;  // computed: 'Jane Doe'

foreach ($person->netIds as $netId) {
    echo $netId->netId;
}

echo $person->email?->emailAddress;
echo $person->address?->streetLine1;
echo $person->address?->city;
echo $person->address?->state?->code;
echo $person->phone?->phoneNumber;
echo $person->title;
```

**Returns:** `Person`

**Underlying endpoint:** `GET /PersonLookup/1_0/{uin}` (uses `Http::aitsPerson()`)

## Azure Person Lookup

Retrieve comprehensive person data from Azure including all names, addresses, emails, phones, biographical data, and employee info.

```php
use Uisits\AitsApi\Request\AzureRequest\AitsAzurePersonLookup;

$person = AitsAzurePersonLookup::get('123456789');

echo $person->identity?->guid;
echo $person->identity?->uin;

foreach ($person->names as $name) {
    echo $name->firstName;
    echo $name->lastName;
    echo $name->type;
}

echo $person->biodemo?->birthDate;
echo $person->biodemo?->gender;

foreach ($person->address as $addr) {
    echo $addr->streetLine1;
    echo $addr->city;
    echo $addr->state;
    echo $addr->type;
}

foreach ($person->email as $email) {
    echo $email->emailAddress;
    echo $email->preferredInd;
}

echo $person->employee?->status;
```

**Returns:** `AzurePerson`

**Underlying endpoint:** `GET /person/person-data-query/{uin}`

## Student GPA (Azure)

Retrieve a student's GPA information (term, level institutional, level overall, transfer).

```php
use Uisits\AitsApi\Request\AzureRequest\AitsAzureStudentGpa;

$gpaData = AitsAzureStudentGpa::get('123456789', '120241', 'UG');

echo $gpaData->queryUIN;
echo $gpaData->queryTermCode;
echo $gpaData->queryLevelCode;

// Term GPA (current term only)
echo $gpaData->termInstitutionalGpa?->gpa;
echo $gpaData->termInstitutionalGpa?->hours;

// Level institutional GPA (all courses at this institution, this level)
echo $gpaData->levelInstitutionalGpa?->gpa;
echo $gpaData->levelInstitutionalGpa?->hours;

// Level overall GPA (institution + transfer)
echo $gpaData->levelOverallGpa?->gpa;
echo $gpaData->levelOverallGpa?->hours;

// Level transfer GPA
echo $gpaData->levelTransferGpa?->gpa;
echo $gpaData->levelTransferGpa?->hours;
```

**Returns:** `AzureStudentGpa`

**Parameters:** `uin`, `termCode`, `level` (`'UG'`/`'GR'`)

**Underlying endpoint:** `GET /student/student-gpas-query/student-gpas-query/{uin}/{termCode}/{level}`

## High School GPA (Azure)

Retrieve a student's high school GPA and academic subject data.

```php
use Uisits\AitsApi\Request\AzureRequest\AitsAzureStudentHighSchoolGpa;

$hsData = AitsAzureStudentHighSchoolGpa::get('123456789');

foreach ($hsData->highSchools as $highSchool) {
    echo $highSchool->name;
    echo $highSchool->gpa;

    foreach ($highSchool->subjects as $subject) {
        echo $subject->code;
        echo $subject->description;
    }
}
```

**Returns:** `AzureStudentHsGpa`

**Underlying endpoint:** `GET /person/high-school-query/{uin}`

## Race & Ethnicity (Azure)

Retrieve a student's race and ethnicity information.

```php
use Uisits\AitsApi\Request\AzureRequest\AitsAzureRaceEthnicity;

$raceEthnicity = AitsAzureRaceEthnicity::get('123456789');

echo $raceEthnicity->pidm;
echo $raceEthnicity->uin;
echo $raceEthnicity->oldEthnicity;

// IPEDS ethnicity classification
echo $raceEthnicity->validEthnicity->code;
echo $raceEthnicity->validEthnicity->description;

// Race collection
foreach ($raceEthnicity->race as $race) {
    echo $race->validRace?->code;
    echo $race->validRace?->description;
}

// Self-reported confirmation
echo $raceEthnicity->raceEthnicityConfirmation->confirmationInd;
echo $raceEthnicity->raceEthnicityConfirmation->confirmationDate;
```

**Returns:** `RaceEthnicity`

**Underlying endpoint:** `GET /person/race-ethnicity-query/{uin}`

## Employee Data (Azure)

Retrieve basic employment information for a person.

```php
use Uisits\AitsApi\Request\AzureRequest\AitsAzureBasicEmployee;

$employee = AitsAzureBasicEmployee::get('123456789');

echo $employee->status;              // 'A' (Active), 'T' (Terminated)
echo $employee->timeStatus;          // 'F' (Full-time), 'P' (Part-time)
echo $employee->group;
echo $employee->institutionalId;     // UIN
echo $employee->userid;
echo $employee->flsa;

echo $employee->validCampus?->code;
echo $employee->homeOrganization?->code;
echo $employee->homeOrganization?->description;
echo $employee->homeCollege?->code;
echo $employee->validEmployeeClass?->code;

// Employment dates
echo $employee->employmentDates?->hireDate;
echo $employee->employmentDates?->originalHireDate;

// Termination info (if applicable)
echo $employee->employmentTermination?->terminationDate;
echo $employee->employmentTermination?->terminationReason;
```

**Returns:** `AzureEmployee`

**Underlying endpoint:** `GET /employee/basic-employee-query/{uin}`

## Azure Course Detail

Retrieve detailed course section information from Azure (mirrors the Banner course detail endpoint, same `CourseDetail` structure).

```php
use Uisits\AitsApi\Request\AzureRequest\AitsAzureCourseDetail;

$details = AitsAzureCourseDetail::get('120241', '12345');

foreach ($details as $course) {
    echo $course->crn;
    echo $course->subject->code;
    echo $course->number;
    echo $course->title;
    echo $course->sectionEnrollment;
    echo $course->sectionMaxEnrollment;
    echo $course->sectionAvailableSeats;

    $primary = $course->instructor->primaryInstructor()->first();
    echo $primary?->firstName . ' ' . $primary?->lastName;
}
```

**Returns:** `Collection<CourseDetail>` — same structure as `AitsCourseDetail::get()`

**Underlying endpoint:** `GET /student-course/course-detail-query/{term}/{crn}`

## InstructorCollection Helpers

`CourseDetail::$instructor` is an `InstructorCollection` (extends `Illuminate\Support\Collection`) with these domain-specific methods:

### primaryInstructor()

```php
public function primaryInstructor(): ?InstructorCollection
```

Returns instructors where `primaryInd === 'Y'`.

```php
$primary = $course->instructor->primaryInstructor()->first();
echo $primary?->firstName . ' ' . $primary?->lastName;
```

### getLectureInstructors()

```php
public function getLectureInstructors(): InstructorCollection
```

Returns instructors assigned to lecture sessions (`sessionInstructorInd` starts with `'L'`).

```php
foreach ($course->instructor->getLectureInstructors() as $instructor) {
    echo $instructor->uin . ': ' . $instructor->lastName;
}
```

### getLabInstructors()

```php
public function getLabInstructors(): InstructorCollection
```

Returns instructors assigned to lab sessions (`sessionInstructorInd` starts with `'B'`).

```php
foreach ($course->instructor->getLabInstructors() as $instructor) {
    echo $instructor->lastName;
}
```

## Response Objects

All response objects are `readonly` Spatie LaravelData objects. Access properties directly. Nullable properties (`?type`) may be `null` — use the null-safe operator `?->`.

### Core value objects (appear across multiple responses)

| Class | Properties |
|-------|-----------|
| `ValidTerm` | `code`, `description` |
| `ValidPartTerm` | `code`, `description` |
| `ValidRegistrationStatus` | `code`, `description` |
| `ValidCourseRegistrationLevel` | `code`, `description` |
| `ValidEnrollmentStatus` | `code`, `description` |
| `ValidGradingMode` | `code`, `description` |
| `Subject` | `code`, `description` |
| `Campus` | `code`, `description` |
| `ScheduleType` | `code`, `description` |

### GPA object

```php
$gpa->gpa;    // float
$gpa->hours;  // float (total credit hours)
```

### Instructor object

```php
$instructor->uin;
$instructor->primaryInd;        // 'Y' or 'N'
$instructor->firstName;
$instructor->middleName;
$instructor->lastName;
$instructor->sessionInstructorInd; // 'L' = lecture, 'B' = lab
```

## Quick Reference

| Request Class | Method | Parameters | Returns | Source |
|--------------|--------|------------|---------|--------|
| `AitsStudentEnrollment` | `get` | `uin, term` | `StudentEnrollment` | Banner |
| `AitsStudentRoster` | `get` | `term, crn` | `Collection<StudentRoster>` | Banner |
| `AitsStudentHold` | `get` | `uin` | `StudentHold` | Banner |
| `AitsStudentHold` | `put` | `uin` | `Collection<StudentHold>` | Banner |
| `AitsStudentAttribute` | `get` | `uin, term` | `StudentAttribute` | Banner |
| `AitsStudentOverride` | `get` | `uin, term` | `StudentOverride` | Banner |
| `AitsStudentOverride` | `update` | `term, pidm, crn, code, desc` | `bool` | Banner |
| `AitsStudentOverride` | `delete` | `term, pidm, crn, code, desc` | `bool` | Banner |
| `AitsStudentAdvisor` | `get` | `uin, term` | `StudentAdvisor` | Banner |
| `AitsBasicLearner` | `get` | `uin, term` | `BasicLearner` | Banner |
| `AitsCourseDetail` | `get` | `term, crn` | `Collection<CourseDetail>` | Banner |
| `AitsCourseSummary` | `get` | `term` | `Collection<CourseSummary>` | Banner |
| `AitsPersonLookup` | `get` | `uin` | `Person` | Banner |
| `AitsAzureStudentGpa` | `get` | `uin, termCode, level` | `AzureStudentGpa` | Azure |
| `AitsAzureStudentHighSchoolGpa` | `get` | `uin` | `AzureStudentHsGpa` | Azure |
| `AitsAzurePersonLookup` | `get` | `uin` | `AzurePerson` | Azure |
| `AitsAzureRaceEthnicity` | `get` | `uin` | `RaceEthnicity` | Azure |
| `AitsAzureBasicEmployee` | `get` | `uin` | `AzureEmployee` | Azure |
| `AitsAzureCourseDetail` | `get` | `term, crn` | `Collection<CourseDetail>` | Azure |
