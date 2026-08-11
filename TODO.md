# TODO

## Critical Bugs

### 2. Duplicated URL path segment in `AitsAzureStudentGpa`
**File:** `src/Request/AzureRequest/AitsAzureStudentGpa.php:23`

The URL contains a doubled path segment:
`/student/student-gpas-query/student-gpas-query/{uin}/{term}/{level}`

This will produce a 404 from the upstream API. It should likely be:
`/student/student-gpas-query/{uin}/{termCode}/{level}`

---

## Security Issues

### 3. Proxy credentials exposed in plain-text URL string
**File:** `src/AitsServiceProvider.php:33-40`

The proxy URL is built as `scheme://username:password@host:port`. If requests are logged (Telescope, debug mode, or exception dumps), credentials will appear in plain text. Consider using Guzzle's separate `auth` option or ensure logging is disabled in production for this service.

---

### 6. Missing test coverage
No tests exist for:
- Azure request classes
- `AitsStudentAdvisor`, `AitsPersonLookup`, `AitsBasicLearner`
- HTTP failure / error response paths
- Empty-list responses for Banner API endpoints
- `AitsStudentOverride::delete()`
