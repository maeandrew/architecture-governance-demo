# ADR-0009: Documented exception fixture

## Status
Accepted

## Context
This fixture exists to verify that a documented exception is accepted.

## Decision
The dependency `Modules\Booking\Domain\BookingPolicy -> Modules\Catalog\Domain\EventStatus` is intentionally allowed.

## Consequences
- The fixture demonstrates the positive path for documented exceptions.
- Reviewers can confirm the skip rule stays narrow.

## Review
This ADR should be reviewed with the rest of the architecture decisions.
