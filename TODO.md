# TODO

## Bugs

- [ ] Verify `AitsStudentOverride::delete()` return semantics; current behavior intentionally returns the inverse of the API `result`.
- [ ] Confirm the expected HTTP method and response shape for `AitsStudentHold::put()` against the upstream AITS API.
- [ ] Add response fixtures for every request class so DTO mappings stay aligned with upstream payloads.
- [ ] Audit Azure endpoint paths, especially `student-gpas-query/student-gpas-query`, against current API documentation.

## Improvements

- [ ] Add consistent exception types for transport errors, empty single-record responses, and DTO mapping failures.
- [ ] Add public usage examples for every request class in `README.md`.
- [ ] Add static analysis once DTO nullability is aligned with real API payloads.
- [ ] Consider an injectable client/service layer instead of static request methods for easier testing and mocking.
