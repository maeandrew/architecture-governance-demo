# Contributing

## Verification

```bash
ddev composer install
ddev exec vendor/bin/pest --testsuite=Architecture
ddev exec vendor/bin/deptrac analyse --no-cache --fail-on-uncovered --report-uncovered
ddev exec vendor/bin/phpstan analyse --memory-limit=2G
ddev exec vendor/bin/pest --testsuite=Unit
ddev exec vendor/bin/pest --testsuite=Feature
ddev exec bin/assert-deptrac-fails
```

## Adding a module

1. Create the module directory under `Modules/<Name>/`.
2. Add at least `Contracts/`, `Domain/`, and `Services/` or `Actions/` as needed.
3. Keep calls between modules on interface contracts only.
4. Add the module's layers and rules to `deptrac.layers.yaml`. The Pest naming checks discover new modules automatically.

## Adding a rule or exception

- Cross-module boundary rules: update `deptrac.layers.yaml`. Both the main config and the negative fixture import it.
- Naming or namespace rules: add a Pest test under `tests/Architecture`.
- Exceptions: add the exact class pair to `skip_violations` in `deptrac.yaml` and reference it as `Source -> Target` in an ADR under `docs/adr/`.

## Pull request expectations

- All checks must pass.
- Tests must cover the change.
- Any exception needs a matching ADR and explanation.
- Do not hide real debt with a broad skip.
