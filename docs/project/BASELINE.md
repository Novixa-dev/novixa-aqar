# Upstream Baseline Documentation (TASK-001)

## Executive Summary
This document establishes the verified, empirical baseline for the **Aqar / عقاري** project (based on Liberu Real Estate Laravel).

## Repository & Environment Information
- **Repository Path**: `c:\Users\ak01redwan\Desktop\mywork\aqar-project\aqar`
- **Active Git Branch**: `feature/aqar-productization`
- **Upstream Project**: Liberu Real Estate (`liberu-real-estate/real-estate-laravel`)
- **Framework Version**: Laravel Framework `13.24.0`
- **PHP Version**: `8.3 / 8.4` (Windows CLI execution context)
- **Composer Version**: Composer 2.x with optimized autoloader classmaps (8,684 classes)
- **Testing Database**: In-Memory SQLite (`:memory:`) with custom trigonometric math functions (`acos`, `cos`, `sin`, `radians`)
- **Memory Limit for Test Runner**: `512M` (configured in `phpunit.xml`)

## Verification Results

| Component | Status | Command Executed | Result / Output |
|---|---|---|---|
| **Framework Boot** | PASS | `php artisan --version` | `Laravel Framework 13.24.0` |
| **Dependencies & Autoloader** | PASS | `composer install --ignore-platform-reqs --prefer-dist --no-scripts` | Discovered packages: Filament, Shield, Sanctum, Reverb, Livewire, Spatie |
| **Community Events Feature** | PASS | `php artisan test tests/Feature/CommunityEventTest.php` | 6 passed (14 assertions) |
| **Full Unit & Feature Test Suite** | PASS | `php artisan test` | **670 passed**, 32 skipped, **0 failed** (2,338 assertions verified) |

## Discovered Packages & Extensions
- `filament/filament` & `filament/shield` (Admin panel & RBAC authorization)
- `laravel/jetstream`, `laravel/fortify`, `laravel/sanctum` (Authentication & API Tokens)
- `livewire/livewire` (Interactive reactive UI components)
- `spatie/laravel-permission` (Role-based access control)
- `barryvdh/laravel-debugbar` (Development diagnostics)

## Known Environment Configuration Notes
1. **PHP CLI Memory Limit**: The default Windows PHP CLI memory limit is 128M. `phpunit.xml` has been updated with `<ini name="memory_limit" value="512M"/>` to ensure test runner worker processes do not exceed memory during Blade compilation.
2. **SQLite Trigonometric Functions**: Registered `acos`, `cos`, `sin`, and `radians` via `PDO::sqliteCreateFunction` in `tests/TestCase.php` to enable spatial radius searching in SQLite testing environments.

## Conclusion
The baseline setup is 100% verified, stable, and ready for development.
