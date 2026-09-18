# ADR-0002: Booking policy status lookup exception

## Status
Accepted

## Context
`Booking` must check whether a catalog event is available before creating a reservation. `CreateBookingAction` asks `EventCatalogInterface::statusOf()` for the status and passes it to `BookingPolicy::canPlaceBooking()`, which refuses unavailable events.

The boundary rules forbid `Booking` from depending on `Catalog` internals and forbid module contracts from exposing internal types. `EventStatus` is the smallest signal the policy needs, so it is shared across the boundary instead of promoting the whole `Catalog\Domain` namespace.

## Decision
We allow exactly two class-to-class dependencies, recorded in `deptrac.yaml` under `skip_violations`:

- `Modules\Catalog\Contracts\EventCatalogInterface -> Modules\Catalog\Domain\EventStatus`: the Catalog contract returns the status value object.
- `Modules\Booking\Domain\BookingPolicy -> Modules\Catalog\Domain\EventStatus`: the Booking policy evaluates it.

`CreateBookingAction` only passes the value through and does not reference `EventStatus` by name, so it needs no exception.

## Consequences
- Booking cannot place reservations for unavailable events, and the rule lives in one policy class.
- Both exceptions stay visible to reviewers, and CI checks that each one is documented here.
- Any other class that references `EventStatus` from outside `Catalog` fails the boundary check.

## Review
Revisit this ADR when the booking policy needs more than availability. At that point, move the status into `Catalog\Contracts` as a public DTO and remove both skips.
