# Architecture governance demo

[![Architecture checks](https://github.com/maeandrew/architecture-governance-demo/actions/workflows/architecture.yml/badge.svg)](https://github.com/maeandrew/architecture-governance-demo/actions/workflows/architecture.yml)

This repository is a small, open-source reference implementation for enforcing module boundaries in a Laravel-style PHP codebase without depending on the Laravel framework itself.

The value here is not a production app; it is a reproducible demonstration of clear architectural contracts, explicit exceptions, and automated enforcement.

Unlike baseline-based setups that freeze existing violations in bulk, every exception here is an explicit, ADR-backed class pair, and CI verifies that each one stays documented.

## Module map

```text
Modules/
├── Catalog/    events and their availability
├── Booking/    reservations and the booking policy
├── Billing/    invoices
├── Identity/   user accounts
└── Shared/     Id, Money, and a clock
```

Each module exposes interfaces from `Contracts/`. Its `Http/Controllers`, `Actions`,
`Services`, `Domain`, and `Infrastructure` directories are internal implementation
details. Booking consumes Catalog and Identity contracts; Billing consumes Booking
and Identity contracts; Shared has no module dependencies.

```text
Booking ──► Catalog\Contracts, Identity\Contracts, Shared
Billing ──► Booking\Contracts, Identity\Contracts, Shared
Catalog ──► Shared
Identity ─► Shared
```

The rules live in `deptrac.layers.yaml`, which both `deptrac.yaml` and the negative
fixture import.

## Why Deptrac, Pest arch, and PHPStan

- Deptrac is the main tool for module-to-module dependency boundaries.
- Pest architecture tests cover naming and namespace conventions that are easy to reason about in one file.
- PHPStan enforces signatures and types across the codebase.

Deptrac is primary because it is built around layer definitions and explicit allow/deny rules. Pest arch provides focused assertions for naming and conventions. PHPStan is not a boundary tool; it is a static type checker. PHPArkitect is comparable in spirit, but this repository intentionally does not install it. Larastan is a useful addon for real Laravel projects, but this demo stays framework-free.

## Local setup

### DDEV

```bash
ddev start
ddev composer install
ddev exec vendor/bin/pest --testsuite=Architecture
ddev exec vendor/bin/deptrac analyse --no-cache --fail-on-uncovered --report-uncovered
ddev exec vendor/bin/phpstan analyse --memory-limit=2G
ddev exec vendor/bin/pest --testsuite=Unit
ddev exec vendor/bin/pest --testsuite=Feature
ddev exec bin/assert-deptrac-fails
```

### Plain PHP 8.3 + Composer

```bash
composer install
vendor/bin/pest --testsuite=Architecture
vendor/bin/deptrac analyse --no-cache --fail-on-uncovered --report-uncovered
vendor/bin/phpstan analyse --memory-limit=2G
vendor/bin/pest --testsuite=Unit
vendor/bin/pest --testsuite=Feature
bin/assert-deptrac-fails
```

The repository keeps `.ddev/config.yaml` as a local convenience. The DDEV docroot is `public/`; the minimal `public/index.php` makes the webroot explicit
and prevents project files such as `vendor/` or `composer.json` from being served.

## CI enforcement

The GitHub Actions workflow runs architecture tests, Deptrac, PHPStan, and the unit/feature suite. The final step verifies that a deliberate violation in `fixtures/violations` is rejected with the exact expected message.

If a module boundary is broken, the job fails on the Pest boundary test or on the Deptrac step.

## Adding a rule

1. Decide whether the rule belongs in Deptrac or Pest.
2. If it is a cross-module dependency rule, update the shared `deptrac.layers.yaml` ruleset.
3. If it is a naming or namespace rule, add a dedicated Pest test under `tests/Architecture`.
4. Validate the focused command before merging. The negative fixture imports the same
   shared ruleset, so it cannot silently drift into a weaker policy.

## Allowing an exception

1. Create or update an ADR under `docs/adr/`.
2. Add the exact dependency to `deptrac.yaml` under `skip_violations`.
3. Keep the exception class-to-class and local to the rule.
4. Ensure the ADR and YAML match exactly.

Example allowed dependency:

```php
use Modules\Catalog\Domain\EventStatus;

final class BookingPolicy
{
    public function canPlaceBooking(EventStatus $status): bool
    {
        return $status->isAvailable();
    }
}
```

`CreateBookingAction` reads the status through `EventCatalogInterface::statusOf()` and
refuses unavailable events through this policy. The two class pairs involved
(`BookingPolicy -> EventStatus` and `EventCatalogInterface -> EventStatus`) are allowed only
because they are listed in `skip_violations` and documented in
`docs/adr/ADR-0002-booking-policy-event-status-exception.md`.

Example forbidden dependency:

```php
use Modules\Booking\Domain\BookingState;

final class InvoiceFactory
{
    public function __construct(private BookingState $state) {}
}
```

This is forbidden because `Billing` must not depend on `Booking` internals.

### Excerpt of the intentional violation output

```text
$ ./bin/assert-deptrac-fails
Detected intentional violation:
  Reason                     BillingInternal
  DependsOnDisallowedLayer   Modules\Billing\Domain\InvoiceFactory must not depend on Modules\Booking\Domain\BookingState
  Violations                 1
  Uncovered                  0
  Errors                     0
```

## Documentation and ADRs

Every documented exception must have an ADR, and every ADR must reference the actual skip violation. The tests in `tests/Architecture/DocumentationIntegrityTest.php` enforce that contract.

## Official references

- Deptrac: https://deptrac.github.io/deptrac/
- Pest architecture testing: https://pestphp.com/docs/arch-testing
- PHPStan: https://phpstan.org/
- PHPArkitect: https://phparkitect.org/
- Larastan: https://larastan.com/
- ADR format: https://adr.github.io/

## Why exceptions are not permanent

An exception is an explicit signal that the architecture is not yet ideal. It should be temporary, narrow, and reviewed. The intent is to refactor toward cleaner boundaries, not to add more skips indefinitely.
