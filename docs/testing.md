# Testing & Quality Assurance Guide — GAP Backend

## Overview

The GAP Backend utilizes PHPUnit for automated unit and feature testing.

---

## Test Execution

Run the complete test suite:
```bash
./vendor/bin/phpunit
```

Run specific test files or filters:
```bash
./vendor/bin/phpunit --filter SecurityAuditTest
```

---

## Testing Environment Configuration

In `phpunit.xml`, tests are configured to run in an isolated SQLite in-memory database:

```xml
<env name="APP_ENV" value="testing"/>
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="CACHE_DRIVER" value="array"/>
<env name="MAIL_MAILER" value="array"/>
<env name="QUEUE_CONNECTION" value="sync"/>
```

This configuration guarantees that running automated tests will **never** alter local MySQL databases or connect to external production APIs.
