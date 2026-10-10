# Security Controls & Remediation Guide — GAP Backend

## Overview

This document details the security model, vulnerability remediations, credential management guidelines, and security verification procedures implemented across both remediation passes for the GAP Backend codebase.

---

## Remediated Security Findings

### 1. Administrative Route Authorization & Resource Protection (PASS 2)
- **Problem**: Administrative endpoints in `routes/web.php` (`/services`, `/facility`, `/field`, `/room-field`, `/difficulty`, `/golf-style`, `/show-log`, `/get-log`, `/clear-log`, `/reset-rank`, `/davinci`, `/pulk_publish`) were exposed without route-level authentication or resource-level role authorization.
- **Remediation Implemented**:
  - Implemented `EnsureAdmin` middleware ([app/Http/Middleware/EnsureAdmin.php](file:///Users/sameh/projects/gap/app/Http/Middleware/EnsureAdmin.php)) registered in `Kernel.php` as `admin`.
  - Enforced dual authentication and authorization: Users must be authenticated AND belong to Golf Globe (`CompanyType::GG = 1`) with a non-client role (`role_id != UserDetails::CLIENT_ROLE`).
  - Converted state-changing endpoints (`/clear-log`, `/reset-rank`) to `POST` routes with structured audit logging.
  - Extracted `/reset-rank` maintenance into Artisan command `gap:reset-rank` ([app/Console/Commands/ResetRankCommand.php](file:///Users/sameh/projects/gap/app/Console/Commands/ResetRankCommand.php)).
  - Added input validation rules to `save-*` and `new-*` administrative routes in [routes/web.php](file:///Users/sameh/projects/gap/routes/web.php).

### 2. Dependency Reproducibility & Composer Lockfile (PASS 2)
- **Problem**: `composer.lock` was ignored in `.gitignore`, preventing reproducible dependency installation across environments.
- **Remediation Implemented**:
  - Removed `composer.lock` from [.gitignore](file:///Users/sameh/projects/gap/.gitignore) to ensure lockfile is tracked.
  - Recommended pinning `golfglobe/bewotec-api` from `dev-main` branch tracking to explicit release tags or commit SHAs for production builds.

### 3. Exposure of Environment Files in Version Control (PASS 1)
- **Problem**: `.env.production` containing real database passwords, SMTP credentials, API tokens, and integration keys was tracked in Git.
- **Remediation Implemented**:
  - Removed `.env.production` from Git tracking (`git rm --cached`).
  - Updated `.gitignore` to match `.env.*` pattern while preserving `.env.example`.
  - Sanitized `.env.example` to remove all live secrets and replace them with standard placeholders.

### 4. Embedded Credentials in Composer Repositories (PASS 1)
- **Problem**: `composer.json` included plain-text authentication tokens inside a VCS repository URL (`https://user:token@gitlab.golfglobe.com/...`).
- **Remediation Implemented**:
  - Replaced the tokenized URL with a clean Git URL in `composer.json`.

### 5. Arbitrary SQL Execution Endpoint (PASS 1)
- **Problem**: `routes/api.php` contained a route `GET /api/sql` that accepted raw SQL strings in request query parameters and executed them directly via `DB::select()`.
- **Remediation Implemented**:
  - Deleted the `/api/sql` endpoint entirely.

### 6. Log File Management & Path Traversal (PASS 1 & 2)
- **Problem**: `routes/web.php` contained endpoints allowing log file deletion (`/delete-logfiles`), directory scanning (`/wright-files-is-log`), and path traversal (`get-log?filename=../../.env`).
- **Remediation Implemented**:
  - Deleted public unauthenticated log deletion and directory scanning endpoints.
  - Enforced strict `basename()` filtering and regex character restrictions (`preg_replace('/[^a-zA-Z0-9_\-]/', '', ...)`) on `get-log` and `clear-log`.

### 7. ApiKey Authentication Middleware Logic Flaw (PASS 1 & 2)
- **Problem**: `app/Http/Middleware/ApiKey.php` used an un-nested `orWhere('no_expire', 1)` clause, allowing invalid tokens to bypass authentication if any integration record had `no_expire = 1`.
- **Remediation Implemented**:
  - Re-grouped database query conditions to guarantee `api_key`, `status`, and `deleted_at` are strictly evaluated.
  - Fixed `\request()` helper usage to use the parameter `$request`.

---

## Automated Test Coverage & Evidence

Automated tests are isolated in SQLite memory (`:memory:`) across two test classes:
- [tests/Feature/AdminRouteSecurityTest.php](file:///Users/sameh/projects/gap/tests/Feature/AdminRouteSecurityTest.php)
- [tests/Feature/ApiKeyMiddlewareTest.php](file:///Users/sameh/projects/gap/tests/Feature/ApiKeyMiddlewareTest.php)
- [tests/Feature/SecurityAuditTest.php](file:///Users/sameh/projects/gap/tests/Feature/SecurityAuditTest.php)

**PHPUnit Verification Result**:
```
OK (20 tests, 49 assertions)
```

---

## Required Manual Action Checklist for Production Engineers

1. **Rotate Credentials**:
   - Issue new passwords for database user `wfs_gap_production`.
   - Rotate Mailtrap API tokens and SMTP passwords.
   - Re-issue `PULK_PUBLISH_TOKEN` JWT tokens.
   - Rotate Bewotec DaVinci integration passwords.

2. **Git History Cleanup (If Applicable)**:
   - If historical commits were pushed to an external/public remote repository before history flattening, ensure remote refs are force-updated cleanly.

3. **Deploy Environment Variables**:
   - Store server secrets directly in server environment variables or restricted host `.env` files.
