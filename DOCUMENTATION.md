# UIS ITS AITS API — Complete API Reference

> **AI Agent Note:** This document is structured for both human and automated consumption.
> Each API section follows a consistent schema:
> `Class → Method → Parameters → Return Type → Exceptions → Example`.
> All request classes use **static methods** — no instantiation needed.
> All responses are **typed Spatie LaravelData objects** — access properties directly.

---

## Table of Contents

1. [Installation & Configuration](#1-installation--configuration)
2. [Package Architecture](#2-package-architecture)
3. [Error Handling](#3-error-handling)
4. [Banner API — Student Endpoints](#4-banner-api--student-endpoints)
   - [AitsStudentEnrollment](#41-aitsstudentstenrollment)
   - [AitsStudentRoster](#42-aitsstudentsroster)
   - [AitsStudentHold](#43-aitsstudentholds)
   - [AitsStudentAttribute](#44-aitsstudentattribute)
   - [AitsStudentOverride](#45-aitsstudentoverride)
   - [AitsStudentAdvisor](#46-aitsstudentadvisor)
   - [AitsBasicLearner](#47-aitsbasiclearner)
5. [Banner API — Course Endpoints](#5-banner-api--course-endpoints)
   - [AitsCourseDetail](#51-aitscoursedelta)
   - [AitsCourseSummary](#52-aitscoursesummary)
6. [Banner API — Person Endpoints](#6-banner-api--person-endpoints)
   - [AitsPersonLookup](#61-aitspersonlookup)
7. [Azure API — Student Endpoints](#7-azure-api--student-endpoints)
   - [AitsAzureStudentGpa](#71-aitsazurestudentgpa)
   - [AitsAzureStudentHighSchoolGpa](#72-aitsazurestudentHighschoolgpa)
8. [Azure API — Person Endpoints](#8-azure-api--person-endpoints)
   - [AitsAzurePersonLookup](#81-aitsazurepersonlookup)
   - [AitsAzureRaceEthnicity](#82-aitsazureraceethncity)
9. [Azure API — Employee Endpoints](#9-azure-api--employee-endpoints)
   - [AitsAzureBasicEmployee](#91-aitsazurebasicemployee)
10. [Azure API — Course Endpoints](#10-azure-api--course-endpoints)
    - [AitsAzureCourseDetail](#101-aitsazurecoursedetail)
11. [Response Object Reference](#11-response-object-reference)
12. [InstructorCollection Helper Methods](#12-instructorcollection-helper-methods)

---

## 1. Installation & Configuration

### Install

Run:

```bash
composer require uisits/aits-api
php artisan vendor:publish --tag=aits-api
```

### Environment Variables

```dotenv
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

### Config File (`config/aits-api.php`)

| Key | Env Variable | Description |
|-----|-------------|-------------|
| `with_proxy` | — | `true`/`false` to enable proxy globally |
| `base_url` | `AITS_BASE_URL` | Banner student/course API base URL |
| `person_base_url` | `AITS_PERSON_BASE_URL` | Banner person API base URL |
| `proxy.scheme` | `AITS_PROXY_SCHEME` | Proxy scheme (`http://`) |
| `proxy.host` | `AITS_PROXY_HOST` | Proxy hostname or IP |
| `proxy.port` | `AITS_PROXY_PORT` | Proxy port |
| `proxy.username` | `AITS_PROXY_USERNAME` | Proxy username |
| `proxy.password` | `AITS_PROXY_PASSWORD` | Proxy password |
| `azure.portal_key` | `AITS_AZURE_PORTAL_KEY` | Azure `Ocp-Apim-Subscription-Key` |
| `azure.base_url` | `AITS_AZURE_BASE_URL` | Azure API base URL |

---

## 2. Package Architecture

```
Uisits\AitsApi\
├── AitsServiceProvider          — Registers Http::aits(), Http::aitsPerson(), Http::aitsAzure() macros
├── Exceptions\
│   └── AitsRequestFailed        — Thrown on any failed API call
├── Request\                     — Static classes that call Banner API endpoints
│   ├── AitsStudentEnrollment
│   ├── AitsStudentRoster
│   ├── AitsStudentHold
│   ├── AitsStudentAttribute
│   ├── AitsStudentOverride
│   ├── AitsStudentAdvisor
│   ├── AitsBasicLearner
│   ├── AitsCourseDetail
│   ├── AitsCourseSummary
│   ├── AitsPersonLookup
│   └── AzureRequest\            — Static classes that call Azure API endpoints
│       ├── AitsAzureStudentGpa
│       ├── AitsAzureStudentHighSchoolGpa
│       ├── AitsAzurePersonLookup
│       ├── AitsAzureRaceEthnicity
│       ├── AitsAzureBasicEmployee
│       └── AitsAzureCourseDetail
└── Response\                    — Typed Spatie LaravelData objects returned by requests
    └── (see Section 11)
```

**HTTP Macros registered by the service provider:**

| Macro | Used By | Auth |
|-------|---------|------|
| `Http::aits()` | Banner student/course requests | None (relies on network/proxy) |
| `Http::aitsPerson()` | Banner person lookup | None |
| `Http::aitsAzure()` | All Azure requests | `Ocp-Apim-Subscription-Key` header |

---

## 3. Error Handling

All request classes throw `Uisits\AitsApi\Exceptions\AitsRequestFailed` on failure.

```php
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $enrollment = AitsStudentEnrollment::get('123456789', '120241');
} catch (AitsRequestFailed $e) {
    // $e->getMessage() — human-readable message
    // $e->getCode()    — HTTP status code (404, 500, etc.)
    // $e->getPrevious() — original exception if wrapped
    logger()->error('AITS error', ['message' => $e->getMessage(), 'code' => $e->getCode()]);
}
```

**Common error codes:**

| Code | Meaning |
|------|---------|
| `404` | Record not found in Banner/Azure |
| `500` | API request failed (network, auth, server error) |

---

## 4. Banner API — Student Endpoints

### 4.1 AitsStudentEnrollment

**Class:** `Uisits\AitsApi\Request\AitsStudentEnrollment`

Retrieves a student's complete enrollment record for a given term.

#### Method: `get`

```php
public static function get(string $uin, string $term): StudentEnrollment
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$uin` | `string` | University Identification Number | `'123456789'` |
| `$term` | `string` | Banner term code (YYYYTT) | `'120241'` (Fall 2024) |

**Returns:** `Uisits\AitsApi\Response\StudentEnrollment\StudentEnrollment`

**Throws:** `AitsRequestFailed` — 404 if student/term not found, 500 on request failure.

**Underlying endpoint:** `GET /StudentEnrollment/1_0/{uin}/{term}`

#### Example

```php
use Uisits\AitsApi\Request\AitsStudentEnrollment;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $enrollment = AitsStudentEnrollment::get('123456789', '120241');

    // Access student identity
    echo $enrollment->lightweightPerson->institutionalId;          // '123456789'
    echo $enrollment->lightweightPerson->name->firstName;         // 'Jane'
    echo $enrollment->lightweightPerson->name->lastName;          // 'Doe'

    // Access enrollment status
    echo $enrollment->validEnrollmentStatus->code;               // 'EL'
    echo $enrollment->validEnrollmentStatus->description;        // 'Eligible'

    // Access term information
    echo $enrollment->validTerm->code;                           // '120241'
    echo $enrollment->validTerm->description;                    // 'Fall 2024'

    // Iterate registered courses
    foreach ($enrollment->courseRegistration as $course) {
        echo $course->courseSection->courseReferenceNumber;      // '12345'
        echo $course->validRegistrationStatus->code;             // 'RW'
        echo $course->validGradingMode->code;                    // 'Standard'
        echo $course->validCourseRegistrationLevel->code;        // 'UG'
    }
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

### 4.2 AitsStudentRoster

**Class:** `Uisits\AitsApi\Request\AitsStudentRoster`

Retrieves the full roster of students enrolled in a specific course section.

#### Method: `get`

```php
public static function get(string $term, string $crn): \Illuminate\Support\Collection
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$term` | `string` | Banner term code | `'120241'` |
| `$crn` | `string` | Course Reference Number | `'12345'` |

**Returns:** `Collection<StudentRoster>` — may be empty if no students are enrolled.

**Throws:** `AitsRequestFailed` — 500 on request failure.

**Underlying endpoint:** `GET /StudentRoster/1_0/{term}/{crn}`

#### Example

```php
use Uisits\AitsApi\Request\AitsStudentRoster;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $roster = AitsStudentRoster::get('120241', '12345');

    echo $roster->count(); // number of enrolled students

    foreach ($roster as $student) {
        echo $student->lightweightPerson->institutionalId;       // UIN
        echo $student->lightweightPerson->name->firstName;
        echo $student->lightweightPerson->name->lastName;
        echo $student->courseReferenceNumber;                    // '12345'
        echo $student->validTerm->code;                          // '120241'
        echo $student->validRegistrationStatus->code;            // registration status

        // Email addresses
        foreach ($student->email as $email) {
            echo $email->emailAddress;
            echo $email->preferredInd;                           // 'Y' or 'N'
        }
    }
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

### 4.3 AitsStudentHold

**Class:** `Uisits\AitsApi\Request\AitsStudentHold`

Retrieves or releases student holds for a given UIN.

#### Method: `get` — Retrieve holds

```php
public static function get(string $uin): StudentHold
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$uin` | `string` | University Identification Number | `'123456789'` |

**Returns:** `Uisits\AitsApi\Response\StudentHold\StudentHold`

**Throws:** `AitsRequestFailed` — 404 if no holds record found, 500 on request failure.

**Underlying endpoint:** `GET /StudentHolds/1_0/{uin}`

#### Method: `put` — Release/update holds

```php
public static function put(string $uin): \Illuminate\Support\Collection
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$uin` | `string` | University Identification Number | `'123456789'` |

**Returns:** `Collection<StudentHold>` — may be empty after release.

**Throws:** `AitsRequestFailed` — 500 on failure.

**Underlying endpoint:** `PUT /StudentHolds/1_0/{uin}`

#### Example

```php
use Uisits\AitsApi\Request\AitsStudentHold;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

// --- Retrieve holds ---
try {
    $holdRecord = AitsStudentHold::get('123456789');

    echo $holdRecord->queryUIN;                  // '123456789'
    echo $holdRecord->person->uin;
    echo $holdRecord->person->name->firstName;

    if ($holdRecord->hold->isEmpty()) {
        echo 'No holds on this student.';
    }

    foreach ($holdRecord->hold as $hold) {
        echo $hold->guid;
        echo $hold->fromDate?->format('Y-m-d');  // Carbon date
        echo $hold->toDate?->format('Y-m-d');
        echo $hold->holdType?->code;
        echo $hold->holdType?->description;
        echo $hold->holdOrigin?->code;
        echo $hold->holdReason?->description;
        echo $hold->holdComment;
        echo $hold->releaseInd;                  // 'Y' if releaseable
    }
} catch (AitsRequestFailed $e) {
    // handle error
}

// --- Release/update holds ---
try {
    $updatedHolds = AitsStudentHold::put('123456789');
    // $updatedHolds is a Collection<StudentHold> (may be empty)
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

### 4.4 AitsStudentAttribute

**Class:** `Uisits\AitsApi\Request\AitsStudentAttribute`

Retrieves student attributes (custom Banner attribute codes) for a student/term combination.

#### Method: `get`

```php
public static function get(string $uin, string $term): StudentAttribute
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$uin` | `string` | University Identification Number | `'123456789'` |
| `$term` | `string` | Banner term code | `'120241'` |

**Returns:** `Uisits\AitsApi\Response\StudentAttribute\StudentAttribute`

**Throws:** `AitsRequestFailed` — 404 if not found, 500 on failure.

**Underlying endpoint:** `GET /StudentAttributes/1_0/{uin}/400/{term}`

> **Note:** The campus code `400` (UIS) is hardcoded in the endpoint path.

#### Example

```php
use Uisits\AitsApi\Request\AitsStudentAttribute;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $attributes = AitsStudentAttribute::get('123456789', '120241');

    echo $attributes->queryUIN;              // '123456789'
    echo $attributes->queryCampusCode;       // '400'
    echo $attributes->queryTermCode;         // '120241'
    echo $attributes->person->uin;

    foreach ($attributes->attribute as $attr) {
        echo $attr->code;
        echo $attr->description;
        // Access nested attribute details if present
    }
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

### 4.5 AitsStudentOverride

**Class:** `Uisits\AitsApi\Request\AitsStudentOverride`

Retrieves, creates, or deletes student registration overrides (prerequisite/capacity waivers).

#### Method: `get` — Retrieve overrides

```php
public static function get(string $uin, string $term): StudentOverride
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$uin` | `string` | University Identification Number | `'123456789'` |
| `$term` | `string` | Banner term code | `'120241'` |

**Returns:** `Uisits\AitsApi\Response\StudentOverride\StudentOverride`

**Throws:** `AitsRequestFailed` — 404 if not found, 500 on failure.

**Underlying endpoint:** `GET /StudentRegistrationOverrides/1_0/{uin}/{term}`

#### Method: `update` — Create/grant an override

```php
public static function update(
    string $term,
    string $pidm,
    string $crn,
    string $overrideCode,
    string $overrideDescription
): bool
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$term` | `string` | Banner term code | `'120241'` |
| `$pidm` | `string` | Banner PIDM (internal person ID) | `'99887766'` |
| `$crn` | `string` | Course Reference Number | `'12345'` |
| `$overrideCode` | `string` | Override rule code | `'PREQ'` |
| `$overrideDescription` | `string` | Override rule description | `'Prerequisite Override'` |

**Returns:** `bool` — `true` on success, `false` on API failure.

**Throws:** `AitsRequestFailed` — on network/server error.

**Underlying endpoint:** `POST /StudentRegistrationOverride/1_0/`

#### Method: `delete` — Remove an override

```php
public static function delete(
    string $term,
    string $pidm,
    string $crn,
    string $overrideCode,
    string $overrideDescription
): bool
```

Same parameters as `update`.

**Returns:** `bool` — `true` on successful deletion, `false` on API failure.

**Throws:** `AitsRequestFailed` — on network/server error.

**Underlying endpoint:** `POST /StudentRegistrationOverride/1_0/?delete=true`

#### Example

```php
use Uisits\AitsApi\Request\AitsStudentOverride;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

// --- Retrieve overrides ---
try {
    $overrides = AitsStudentOverride::get('123456789', '120241');

    echo $overrides->queryUIN;
    echo $overrides->queryTermCode;
    echo $overrides->person->name->firstName;

    foreach ($overrides->overrides as $override) {
        echo $override->crn;
        echo $override->rule?->code;
        echo $override->rule?->description;
    }
} catch (AitsRequestFailed $e) {
    // handle error
}

// --- Grant a prerequisite override ---
try {
    $success = AitsStudentOverride::update(
        term: '120241',
        pidm: '99887766',
        crn: '12345',
        overrideCode: 'PREQ',
        overrideDescription: 'Prerequisite Override'
    );

    if ($success) {
        echo 'Override granted.';
    }
} catch (AitsRequestFailed $e) {
    // handle error
}

// --- Remove an override ---
try {
    $deleted = AitsStudentOverride::delete(
        term: '120241',
        pidm: '99887766',
        crn: '12345',
        overrideCode: 'PREQ',
        overrideDescription: 'Prerequisite Override'
    );

    if ($deleted) {
        echo 'Override removed.';
    }
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

### 4.6 AitsStudentAdvisor

**Class:** `Uisits\AitsApi\Request\AitsStudentAdvisor`

Retrieves a student's academic advisor(s) for a given term.

#### Method: `get`

```php
public static function get(string $uin, string $term): StudentAdvisor
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$uin` | `string` | University Identification Number | `'123456789'` |
| `$term` | `string` | Banner term code | `'120241'` |

**Returns:** `Uisits\AitsApi\Response\StudentAdvisor\StudentAdvisor`

**Throws:** `AitsRequestFailed` — 404 if not found, 500 on failure.

**Underlying endpoint:** `GET /StudentAdvisors/1_0/{uin}/{term}`

#### Example

```php
use Uisits\AitsApi\Request\AitsStudentAdvisor;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $advisorRecord = AitsStudentAdvisor::get('123456789', '120241');

    echo $advisorRecord->queryUIN;
    echo $advisorRecord->queryTermCode;
    echo $advisorRecord->person->name->firstName;

    if ($advisorRecord->advisors?->isEmpty()) {
        echo 'No advisors assigned.';
    }

    foreach ($advisorRecord->advisors as $advisor) {
        // Computed convenience properties on Advisor
        echo $advisor->fullName;          // 'John Smith' (computed)
        echo $advisor->firstName;         // 'John' (computed from nested person)
        echo $advisor->lastName;          // 'Smith' (computed)
        echo $advisor->uin;               // advisor's UIN (computed)
        echo $advisor->pidm;              // advisor's PIDM (computed)

        echo $advisor->primaryAdvisorInd; // 'Y' if primary advisor
        echo $advisor->advisorType?->code;
        echo $advisor->advisorType?->description;
        echo $advisor->advisorTerm?->code;
    }
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

### 4.7 AitsBasicLearner

**Class:** `Uisits\AitsApi\Request\AitsBasicLearner`

Retrieves a student's academic program record for a given term (major, college, degree, level, etc.).

#### Method: `get`

```php
public static function get(string $uin, string $term): BasicLearner
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$uin` | `string` | University Identification Number | `'123456789'` |
| `$term` | `string` | Banner term code | `'120241'` |

**Returns:** `Uisits\AitsApi\Response\BasicLearner\BasicLearner`

**Throws:** `AitsRequestFailed` — 404 if not found, 500 on failure.

**Underlying endpoint:** `GET /BasicLearner/1_0/{uin}/{term}`

#### Example

```php
use Uisits\AitsApi\Request\AitsBasicLearner;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $learner = AitsBasicLearner::get('123456789', '120241');

    echo $learner->queryUIN;
    echo $learner->queryTermCode;

    // Person identity
    echo $learner->person->uin;
    echo $learner->person->name->firstName;
    echo $learner->person->name->lastName;

    // Student class (e.g. Freshman, Junior)
    echo $learner->studentClass?->code;
    echo $learner->studentClass?->description;

    // Academic program (StudentRecord contains aggregated collections)
    $record = $learner->studentRecord;

    echo $record->campus?->code;
    echo $record->campus?->description;
    echo $record->level1?->code;           // Academic level (UG, GR, etc.)

    // Aggregated collections (always safe to iterate, even if empty)
    foreach ($record->majors as $major) {
        echo $major->code;
        echo $major->description;
    }

    foreach ($record->colleges as $college) {
        echo $college->code;
    }

    foreach ($record->departments as $dept) {
        echo $dept->code;
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

    foreach ($record->programs as $program) {
        echo $program->code;
    }
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

## 5. Banner API — Course Endpoints

### 5.1 AitsCourseDetail

**Class:** `Uisits\AitsApi\Request\AitsCourseDetail`

Retrieves detailed information about a course section including instructors, meeting times, enrollment counts, and cross-list data.

#### Method: `get`

```php
public static function get(string $term, string $crn): \Illuminate\Support\Collection
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$term` | `string` | Banner term code | `'120241'` |
| `$crn` | `string` | Course Reference Number | `'12345'` |

**Returns:** `Collection<CourseDetail>` — typically one item; may be multiple for cross-listed sections.

**Throws:** `AitsRequestFailed` — 500 on failure.

**Underlying endpoint:** `GET /CourseDetail/1_0/{term}/{crn}`

#### Example

```php
use Uisits\AitsApi\Request\AitsCourseDetail;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $details = AitsCourseDetail::get('120241', '12345');

    foreach ($details as $course) {
        echo $course->term;                          // '120241'
        echo $course->crn;                           // '12345'
        echo $course->subject->code;                 // 'CS'
        echo $course->subject->description;          // 'Computer Science'
        echo $course->number;                        // '101'
        echo $course->title;                         // 'Intro to Programming'
        echo $course->sectionNumber;                 // '001'
        echo $course->sectionDescription;

        // Status and type
        echo $course->sectionStatus->code;
        echo $course->scheduleType->code;            // 'LEC', 'LAB', etc.
        echo $course->gradableInd;                   // 'Y' or 'N'

        // Enrollment
        echo $course->sectionMaxEnrollment;          // 30
        echo $course->sectionEnrollment;             // 25
        echo $course->sectionAvailableSeats;         // 5
        echo $course->sectionWaitCount;
        echo $course->sectionWaitCapacity;
        echo $course->sectionWaitAvail;

        // Meeting info
        echo $course->sectionMeetingDays;            // 'MWF'
        echo $course->sectionMeetingHours;           // '0900-0950'
        echo $course->sectionMeetingDates;
        echo $course->sectionBuildingDescription;
        echo $course->sectionRoomNumber;

        // Cross-listing
        echo $course->crossListGroupID;
        echo $course->crossListSectionMaxEnrollment;
        echo $course->crossListSectionEnrollment;
        echo $course->crossListSectionAvailableSeats;

        // Instructors (InstructorCollection — see Section 12)
        $primary = $course->instructor->primaryInstructor()->first();
        echo $primary?->firstName . ' ' . $primary?->lastName;

        foreach ($course->instructor->getLectureInstructors() as $instructor) {
            echo $instructor->uin;
            echo $instructor->firstName . ' ' . $instructor->lastName;
            echo $instructor->primaryInd;
        }

        // Optional nested objects
        echo $course->specialApproval?->code;
        echo $course->sectionPartOfTerm?->code;
        echo $course->sectionMeetingType?->code;
        echo $course->sectionSession?->code;
        echo $course->sectionBuilding?->code;
    }
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

### 5.2 AitsCourseSummary

**Class:** `Uisits\AitsApi\Request\AitsCourseSummary`

Retrieves a summary listing of all course sections offered in a given term.

#### Method: `get`

```php
public static function get(string $term): \Spatie\LaravelData\DataCollection|\Illuminate\Support\Collection
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$term` | `string` | Banner term code | `'120241'` |

**Returns:** A collection of `CourseSummary` objects. Returns an empty collection if no courses are found.

**Throws:** `AitsRequestFailed` — 500 on failure.

**Underlying endpoint:** `GET /CourseSummary/1_0/{term}`

#### Example

```php
use Uisits\AitsApi\Request\AitsCourseSummary;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $courses = AitsCourseSummary::get('120241');

    echo $courses->count(); // total sections in the term

    foreach ($courses as $course) {
        echo $course->term;              // '120241'
        echo $course->crn;               // course reference number
        echo $course->subject->code;     // 'CS'
        echo $course->subject->description;
        echo $course->number;            // '101'
    }
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

## 6. Banner API — Person Endpoints

### 6.1 AitsPersonLookup

**Class:** `Uisits\AitsApi\Request\AitsPersonLookup`

Retrieves full person identity and contact information from the Banner person service.

#### Method: `get`

```php
public static function get(string $uin): Person
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$uin` | `string` | University Identification Number | `'123456789'` |

**Returns:** `Uisits\AitsApi\Response\Person\Person`

**Throws:** `AitsRequestFailed` — 404 if not found, 500 on failure.

**Underlying endpoint:** `GET /PersonLookup/1_0/{uin}` (uses `Http::aitsPerson()`)

#### Example

```php
use Uisits\AitsApi\Request\AitsPersonLookup;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $person = AitsPersonLookup::get('123456789');

    echo $person->guid;
    echo $person->pidm;
    echo $person->uin;

    // Name
    echo $person->name?->firstName;
    echo $person->name?->lastName;
    echo $person->name?->full_name;  // computed: 'Jane Doe'

    // Net IDs (collection)
    foreach ($person->netIds as $netId) {
        echo $netId->netId;
    }

    // Email
    echo $person->email?->emailAddress;

    // Address
    echo $person->address?->streetLine1;
    echo $person->address?->city;
    echo $person->address?->state?->code;
    echo $person->address?->zip;

    // Phone
    echo $person->phone?->phoneNumber;

    // Title and employee info
    echo $person->title;
    echo $person->employee?->status;
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

## 7. Azure API — Student Endpoints

### 7.1 AitsAzureStudentGpa

**Class:** `Uisits\AitsApi\Request\AzureRequest\AitsAzureStudentGpa`

Retrieves a student's GPA information (term, level institutional, level overall, transfer) from the Azure API.

#### Method: `get`

```php
public static function get(string $uin, string $termCode, string $level): AzureStudentGpa
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$uin` | `string` | University Identification Number | `'123456789'` |
| `$termCode` | `string` | Banner term code | `'120241'` |
| `$level` | `string` | Academic level code | `'UG'` (Undergraduate), `'GR'` (Graduate) |

**Returns:** `Uisits\AitsApi\Response\AzureStudentGpa\AzureStudentGpa`

**Throws:** `AitsRequestFailed` — 404 if not found, 500 on failure.

**Underlying endpoint:** `GET /student/student-gpas-query/student-gpas-query/{uin}/{termCode}/{level}`

#### Example

```php
use Uisits\AitsApi\Request\AzureRequest\AitsAzureStudentGpa;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $gpaData = AitsAzureStudentGpa::get('123456789', '120241', 'UG');

    echo $gpaData->queryUIN;
    echo $gpaData->queryTermCode;
    echo $gpaData->queryLevelCode;

    // Person identity
    echo $gpaData->person?->uin;

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
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

### 7.2 AitsAzureStudentHighSchoolGpa

**Class:** `Uisits\AitsApi\Request\AzureRequest\AitsAzureStudentHighSchoolGpa`

Retrieves a student's high school GPA and academic subject data.

#### Method: `get`

```php
public static function get(string $uin): AzureStudentHsGpa
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$uin` | `string` | University Identification Number | `'123456789'` |

**Returns:** `Uisits\AitsApi\Response\AzureStudentHsGpa\AzureStudentHsGpa`

**Throws:** `AitsRequestFailed` — 404 if not found, 500 on failure.

**Underlying endpoint:** `GET /person/high-school-query/{uin}`

#### Example

```php
use Uisits\AitsApi\Request\AzureRequest\AitsAzureStudentHighSchoolGpa;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $hsData = AitsAzureStudentHighSchoolGpa::get('123456789');

    foreach ($hsData->highSchools as $highSchool) {
        echo $highSchool->name;
        echo $highSchool->gpa;

        foreach ($highSchool->subjects as $subject) {
            echo $subject->code;
            echo $subject->description;
        }
    }
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

## 8. Azure API — Person Endpoints

### 8.1 AitsAzurePersonLookup

**Class:** `Uisits\AitsApi\Request\AzureRequest\AitsAzurePersonLookup`

Retrieves comprehensive person data from the Azure person service including all names, addresses, emails, phones, biographical data, and employee info.

#### Method: `get`

```php
public static function get(string $uin): AzurePerson
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$uin` | `string` | University Identification Number | `'123456789'` |

**Returns:** `Uisits\AitsApi\Response\AzurePerson\AzurePerson`

**Throws:** `AitsRequestFailed` — 404 if not found, 500 on failure.

**Underlying endpoint:** `GET /person/person-data-query/{uin}`

#### Example

```php
use Uisits\AitsApi\Request\AzureRequest\AitsAzurePersonLookup;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $person = AitsAzurePersonLookup::get('123456789');

    // Identity
    echo $person->identity?->guid;
    echo $person->identity?->pidm;
    echo $person->identity?->uin;

    // All names (may include preferred, legal, etc.)
    foreach ($person->names as $name) {
        echo $name->firstName;
        echo $name->lastName;
        echo $name->type;
    }

    // Biographical/demographic data
    echo $person->biodemo?->birthDate;
    echo $person->biodemo?->gender;
    echo $person->biodemo?->citizenType;

    // All addresses
    foreach ($person->address as $addr) {
        echo $addr->streetLine1;
        echo $addr->city;
        echo $addr->state;
        echo $addr->zip;
        echo $addr->type;
    }

    // All emails
    foreach ($person->email as $email) {
        echo $email->emailAddress;
        echo $email->preferredInd;
    }

    // All phone numbers
    foreach ($person->phone as $phone) {
        echo $phone->phoneNumber;
        echo $phone->type;
    }

    // Employee info
    echo $person->employee?->status;
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

### 8.2 AitsAzureRaceEthnicity

**Class:** `Uisits\AitsApi\Request\AzureRequest\AitsAzureRaceEthnicity`

Retrieves a student's race and ethnicity information.

#### Method: `get`

```php
public static function get(string $uin): RaceEthnicity
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$uin` | `string` | University Identification Number | `'123456789'` |

**Returns:** `Uisits\AitsApi\Response\RaceEthnicity\RaceEthnicity`

**Throws:** `AitsRequestFailed` — 404 if not found, 500 on failure.

**Underlying endpoint:** `GET /person/race-ethnicity-query/{uin}`

#### Example

```php
use Uisits\AitsApi\Request\AzureRequest\AitsAzureRaceEthnicity;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
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

    // Self-reported confirmation status
    echo $raceEthnicity->raceEthnicityConfirmation->confirmationInd;
    echo $raceEthnicity->raceEthnicityConfirmation->confirmationDate;
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

## 9. Azure API — Employee Endpoints

### 9.1 AitsAzureBasicEmployee

**Class:** `Uisits\AitsApi\Request\AzureRequest\AitsAzureBasicEmployee`

Retrieves basic employment information for a person from the Azure employee service.

#### Method: `get`

```php
public static function get(string $uin): AzureEmployee
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$uin` | `string` | University Identification Number | `'123456789'` |

**Returns:** `Uisits\AitsApi\Response\AzureBasicEmployee\AzureEmployee`

**Throws:** `AitsRequestFailed` — 500 on failure.

**Underlying endpoint:** `GET /employee/basic-employee-query/{uin}`

#### Example

```php
use Uisits\AitsApi\Request\AzureRequest\AitsAzureBasicEmployee;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $employee = AitsAzureBasicEmployee::get('123456789');

    // Employment status
    echo $employee->status;              // 'A' (Active), 'T' (Terminated), etc.
    echo $employee->timeStatus;          // 'F' (Full-time), 'P' (Part-time)
    echo $employee->group;              // employee group
    echo $employee->workPeriod;
    echo $employee->userid;
    echo $employee->institutionalId;    // UIN
    echo $employee->flsa;               // FLSA classification
    echo $employee->sourceApplication;

    // Campus
    echo $employee->validCampus?->code;
    echo $employee->validCampus?->description;

    // Home organization (primary department)
    echo $employee->homeOrganization?->code;
    echo $employee->homeOrganization?->description;

    // Distribution organization (payroll)
    echo $employee->distributionOrganization?->code;
    echo $employee->distributionOrganization?->description;

    // Classification codes
    echo $employee->validEmployeeClass?->code;
    echo $employee->validEmployeeClass?->description;
    echo $employee->validLeaveCategory?->code;
    echo $employee->validBenefitCategory?->code;

    // Home college
    echo $employee->homeCollege?->code;
    echo $employee->homeCollege?->description;

    // Employment dates
    echo $employee->employmentDates?->hireDate;
    echo $employee->employmentDates?->originalHireDate;
    echo $employee->employmentDates?->seniorityDate;

    // Leave info (if applicable)
    echo $employee->employmentLeave?->leaveCode;
    echo $employee->employmentLeave?->leaveBeginDate;
    echo $employee->employmentLeave?->leaveEndDate;

    // Termination info (if applicable)
    echo $employee->employmentTermination?->terminationDate;
    echo $employee->employmentTermination?->terminationReason;

    // E-Verify
    echo $employee->eVerifyCaseNumber;
    echo $employee->eVerifyEffectiveDate;
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

## 10. Azure API — Course Endpoints

### 10.1 AitsAzureCourseDetail

**Class:** `Uisits\AitsApi\Request\AzureRequest\AitsAzureCourseDetail`

Retrieves detailed course section information from the Azure API (mirrors the Banner course detail endpoint).

#### Method: `get`

```php
public static function get(string $term, string $crn): \Illuminate\Support\Collection
```

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `$term` | `string` | Banner term code | `'120241'` |
| `$crn` | `string` | Course Reference Number | `'12345'` |

**Returns:** `Collection<CourseDetail>` — same structure as `AitsCourseDetail::get()`.

**Throws:** `AitsRequestFailed` — 500 on failure.

**Underlying endpoint:** `GET /student-course/course-detail-query/{term}/{crn}`

#### Example

```php
use Uisits\AitsApi\Request\AzureRequest\AitsAzureCourseDetail;
use Uisits\AitsApi\Exceptions\AitsRequestFailed;

try {
    $details = AitsAzureCourseDetail::get('120241', '12345');

    // Same CourseDetail structure as AitsCourseDetail::get()
    foreach ($details as $course) {
        echo $course->crn;
        echo $course->subject->code;
        echo $course->number;
        echo $course->title;
        echo $course->sectionEnrollment;
        echo $course->sectionMaxEnrollment;
        echo $course->sectionAvailableSeats;

        // Primary instructor via InstructorCollection
        $primary = $course->instructor->primaryInstructor()->first();
        echo $primary?->firstName . ' ' . $primary?->lastName;
    }
} catch (AitsRequestFailed $e) {
    // handle error
}
```

---

## 11. Response Object Reference

> **AI Agent Note:** All response objects are `readonly` Spatie LaravelData objects.
> Access properties directly. Nullable properties (`?type`) may be `null` — use the null-safe operator `?->`.

### Core Value Objects

These appear throughout multiple responses:

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
| `College` | `code`, `description` |
| `Department` | `code`, `description` |
| `ScheduleType` | `code`, `description` |
| `Building` | `code`, `description` |

### Person Objects

| Class | Key Properties |
|-------|---------------|
| `Person` | `guid`, `pidm`, `uin`, `name`, `netIds`, `email`, `address`, `phone`, `title`, `employee` |
| `Person\Name` | `firstName`, `lastName`, `full_name` (computed) |
| `LightWeightPerson` | `name` (PersonName), `institutionalId` |
| `PersonName` | `firstName`, `lastName`, `type`, `fullName` (computed) |

### Student Enrollment Objects

| Class | Key Properties |
|-------|---------------|
| `StudentEnrollment` | `lightweightPerson`, `validEnrollmentStatus`, `validTerm`, `courseRegistration` |
| `RegisteredCourse` | `validRegistrationStatus`, `validGradingMode`, `validCourseRegistrationLevel`, `courseSection` |

### Student Hold Objects

| Class | Key Properties |
|-------|---------------|
| `StudentHold` | `queryUIN`, `person`, `hold` (HoldCollection) |
| `Hold` | `guid`, `pidm`, `fromDate` (Carbon), `toDate` (Carbon), `holdType`, `holdOrigin`, `holdReason`, `holdComment`, `user`, `releaseInd`, `activityDate` |
| `HoldType` | `code`, `description` |
| `HoldOrigin` | `code`, `description` |
| `HoldReason` | `code`, `description` |

### Student Advisor Objects

| Class | Key Properties |
|-------|---------------|
| `StudentAdvisor` | `queryUIN`, `queryTermCode`, `person`, `advisors` (AdvisorCollection) |
| `Advisor` | `person`, `advisorTerm`, `primaryAdvisorInd`, `advisorType`, `firstName`\*, `lastName`\*, `fullName`\*, `uin`\*, `pidm`\* |

\* Computed properties derived from nested `person`.

### BasicLearner / StudentRecord Objects

| Class | Key Properties |
|-------|---------------|
| `BasicLearner` | `queryUIN`, `queryTermCode`, `person`, `studentRecord`, `studentClass` |
| `StudentRecord` | `campus`, `level1`, `level2`, `studentType`, `colleges`, `departments`, `majors`, `minors`, `concentrations`, `programs`, `catalogTerms`, `degrees`, `levels` |

### Course Objects

| Class | Key Properties |
|-------|---------------|
| `CourseDetail` | `term`, `crn`, `subject`, `number`, `title`, `sectionNumber`, `sectionStatus`, `scheduleType`, `gradableInd`, `sectionMaxEnrollment`, `sectionEnrollment`, `sectionAvailableSeats`, `crossListGroupID`, `crossListSectionMaxEnrollment`, `crossListSectionEnrollment`, `crossListSectionAvailableSeats`, `sectionRoomNumber`, `sectionMeetingDays`, `sectionMeetingHours`, `sectionMeetingDates`, `sectionBuildingDescription`, `instructor` (InstructorCollection), `specialApproval`, `sectionPartOfTerm`, `sectionMeetingType`, `sectionMeetingScheduleType`, `sectionSession`, `sectionBuilding`, `sectionWaitAvail`, `sectionWaitCapacity`, `sectionWaitCount` |
| `CourseSummary` | `term`, `crn`, `subject`, `number` |
| `Instructor` | `uin`, `primaryInd`, `firstName`, `middleName`, `lastName`, `sessionInstructorInd` |

### Azure-Specific Objects

| Class | Key Properties |
|-------|---------------|
| `AzurePerson` | `identity`, `names` (NameCollection), `biodemo`, `address` (AddressCollection), `email` (EmailCollection), `phone` (PhoneCollection), `employee` |
| `AzureEmployee` | `status`, `timeStatus`, `group`, `workPeriod`, `userid`, `institutionalId`, `flsa`, `validCampus`, `homeOrganization`, `distributionOrganization`, `validEmployeeClass`, `validLeaveCategory`, `validBenefitCategory`, `homeCollege`, `employmentDates`, `employmentLeave`, `employmentTermination`, `eVerifyCaseNumber`, `eVerifyEffectiveDate` |
| `AzureStudentGpa` | `queryUIN`, `queryTermCode`, `queryLevelCode`, `person`, `termInstitutionalGpa`, `levelInstitutionalGpa`, `levelOverallGpa`, `levelTransferGpa` |
| `Gpa` | `gpa`, `hours` |
| `RaceEthnicity` | `pidm`, `uin`, `oldEthnicity`, `validEthnicity`, `race` (RaceCollection), `raceEthnicityConfirmation` |

---

## 12. InstructorCollection Helper Methods

`CourseDetail::$instructor` is an `InstructorCollection` which extends `Illuminate\Support\Collection` and adds these domain-specific methods:

### `primaryInstructor()`

```php
public function primaryInstructor(): ?InstructorCollection
```

Returns a filtered collection containing only instructors where `primaryInd === 'Y'`.

```php
$primary = $course->instructor->primaryInstructor()->first();
echo $primary?->firstName . ' ' . $primary?->lastName;
```

### `getLectureInstructors()`

```php
public function getLectureInstructors(): InstructorCollection
```

Returns instructors assigned to lecture sessions (`sessionInstructorInd` starts with `'L'`).

```php
foreach ($course->instructor->getLectureInstructors() as $instructor) {
    echo $instructor->uin . ': ' . $instructor->lastName;
}
```

### `getLabInstructors()`

```php
public function getLabInstructors(): InstructorCollection
```

Returns instructors assigned to lab sessions (`sessionInstructorInd` starts with `'B'`).

```php
foreach ($course->instructor->getLabInstructors() as $instructor) {
    echo $instructor->lastName;
}
```

---

## Quick Reference Table

| Request Class | Method | Parameters | Returns | Notes |
|--------------|--------|------------|---------|-------|
| `AitsStudentEnrollment` | `get` | `uin, term` | `StudentEnrollment` | Banner |
| `AitsStudentRoster` | `get` | `term, crn` | `Collection<StudentRoster>` | Banner |
| `AitsStudentHold` | `get` | `uin` | `StudentHold` | Banner |
| `AitsStudentHold` | `put` | `uin` | `Collection<StudentHold>` | Banner — release holds |
| `AitsStudentAttribute` | `get` | `uin, term` | `StudentAttribute` | Banner — campus 400 hardcoded |
| `AitsStudentOverride` | `get` | `uin, term` | `StudentOverride` | Banner |
| `AitsStudentOverride` | `update` | `term, pidm, crn, code, desc` | `bool` | Banner — grant override |
| `AitsStudentOverride` | `delete` | `term, pidm, crn, code, desc` | `bool` | Banner — remove override |
| `AitsStudentAdvisor` | `get` | `uin, term` | `StudentAdvisor` | Banner |
| `AitsBasicLearner` | `get` | `uin, term` | `BasicLearner` | Banner |
| `AitsCourseDetail` | `get` | `term, crn` | `Collection<CourseDetail>` | Banner |
| `AitsCourseSummary` | `get` | `term` | `Collection<CourseSummary>` | Banner |
| `AitsPersonLookup` | `get` | `uin` | `Person` | Banner person service |
| `AitsAzureStudentGpa` | `get` | `uin, termCode, level` | `AzureStudentGpa` | Azure |
| `AitsAzureStudentHighSchoolGpa` | `get` | `uin` | `AzureStudentHsGpa` | Azure |
| `AitsAzurePersonLookup` | `get` | `uin` | `AzurePerson` | Azure |
| `AitsAzureRaceEthnicity` | `get` | `uin` | `RaceEthnicity` | Azure |
| `AitsAzureBasicEmployee` | `get` | `uin` | `AzureEmployee` | Azure |
| `AitsAzureCourseDetail` | `get` | `term, crn` | `Collection<CourseDetail>` | Azure |
