# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a PHP library that wraps the wilayah.id API, providing access to Indonesian administrative region data (provinces, regencies, districts, villages). The library is designed with zero dependencies except ext-json, supporting PHP 7.4+.

## Development Commands

### Testing
```bash
# Run all tests
composer test

# Run tests with coverage report
composer test-coverage

# Run a single test
composer test-filter <TestMethodName>

# Or directly with PHPUnit
vendor/bin/phpunit
vendor/bin/phpunit --filter <TestMethodName>
```

### Dependencies
```bash
# Install dependencies
composer install

# Update dependencies
composer update
```

## Architecture

### Core Components

**Client** (`src/Client.php`)
- Main entry point for the library
- Handles HTTP requests to wilayah.id API using native PHP `file_get_contents()` with stream context
- Base URL: `https://wilayah.id/api`
- Factory methods return resource instances: `provinces()`, `regencies()`, `districts()`, `villages()`
- Configurable timeout (default 30s)

**Resource Pattern**
All resources extend `BaseResource` and follow a consistent pattern:
- **Province**: Top-level administrative division (e.g., DKI Jakarta, code: "31")
- **Regency**: Second-level (cities/kabupaten) under provinces (e.g., Jakarta Selatan, code: "31.74")
- **District**: Third-level (kecamatan) under regencies (e.g., Jagakarsa, code: "31.74.09")
- **Village**: Fourth-level (kelurahan/desa) under districts (e.g., Cipedak, code: "31.74.09.1001")

### Resource Hierarchy & API Endpoints

The API follows a hierarchical structure where each level requires the parent code:
- Provinces: `/provinces.json` (independent, no parent required)
- Regencies: `/regencies/{provinceCode}.json` (requires province code)
- Districts: `/districts/{regencyCode}.json` (requires regency code)
- Villages: `/villages/{districtCode}.json` (requires district code)

### Common Resource Methods

All resources (except Province) implement:
- `byParent(code)` - Get all items under parent (e.g., `byProvince()`, `byRegency()`, `byDistrict()`)
- `find(parentCode, code)` - Find specific item by code
- `findByName(parentCode, name)` - Find item by name (case-insensitive, partial match)
- `search(parentCode, name)` - Search multiple items matching pattern

Province resource has simplified methods since it has no parent:
- `all()` - Get all provinces
- `find(code)` - Find by code
- `findByName(name)` - Find by name
- `search(name)` - Search by pattern

### BaseResource Utilities

`src/Resources/BaseResource.php` provides shared search functionality:
- `searchByName()` - Returns first match (exact or partial, case-insensitive)
- `searchAllByName()` - Returns all matches
- Uses `mb_strtolower()` for multi-byte safe Indonesian character support

### Exception Handling

`WilayahException` is thrown for:
- HTTP request failures (network errors, timeouts)
- JSON parsing errors
- API errors

All public methods that make API calls declare `@throws WilayahException`

## Code Conventions

- **Namespace**: `Silalahi\Wilayah`
- **PSR-4 autoloading**: `Silalahi\Wilayah\` → `src/`
- **Type hints**: All parameters and return types are strictly typed
- **Visibility**: Use explicit visibility modifiers (public/private/protected)
- **Docblocks**: All public methods have complete PHPDoc with `@param`, `@return`, `@throws`

## Response Format

All API responses follow this structure:
```php
[
    'data' => [
        ['code' => '31', 'name' => 'DKI Jakarta'],
        // ... more items
    ],
    'meta' => [
        'administrative_area_level' => 1,
        'updated_at' => '2025-07-04'
    ]
]
```

Resource methods return either:
- Full array response (for list operations like `all()`, `byProvince()`)
- Single item from `data` array (for `find()`, `findByName()`)
- Array of items (for `search()`)

## Testing Notes

- Tests are located in `tests/` directory
- Test namespace: `Silalahi\Wilayah\Tests`
- PHPUnit configuration: `phpunit.xml`
- Coverage source: `src/` directory only (excludes vendor)