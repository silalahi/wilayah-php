# Wilayah PHP Client

A PHP wrapper for the [wilayah.id](https://wilayah.id/) API, providing easy access to Indonesian administrative regions data including provinces, regencies/cities, districts, and villages.

## Features
- 🎯 Clean, fluent API with separated resource classes
- 📦 PSR-4 autoloading compatible
- 🛡️ Exception handling
- 💡 Type hints for better IDE support
- 🔍 Search by name or code
- 🇮🇩 Multi-byte safe (supports Indonesian characters)
- 🚀 Supports PHP 7.4 and above
- 📝 Zero dependencies (except ext-json)

## Installation
You can install this package via Composer:

```bash
composer require silalahi/wilayah-php
```

## Usage

### Basic Usage
```php
<?php

require 'vendor/autoload.php';

use Silalahi\Wilayah\Client;
use Silalahi\Wilayah\Exceptions\WilayahException;

// Create client instance
$client = new Client();

try {
    // Get all provinces
    $provinces = $client->provinces()->all();
    print_r($provinces);
    
    // Get regencies from DKI Jakarta (code: 31)
    $regencies = $client->regencies()->byProvince('31');
    print_r($regencies);
    
    // Get districts from Jakarta Selatan (code: 31.74)
    $districts = $client->districts()->byRegency('31.74');
    print_r($districts);
    
    // Get villages from Jagakarsa (code: 31.74.09)
    $villages = $client->villages()->byDistrict('31.74.09');
    print_r($villages);
    
} catch (WilayahException $e) {
    echo "Error: " . $e->getMessage();
}
```

### Response Format
All methods return an array with the following structure:

```php
[
    'data' => [
        [
            'code' => '31',
            'name' => 'DKI Jakarta'
        ],
        // ... more items
    ],
    'meta' => [
        'administrative_area_level' => 1,
        'updated_at' => '2025-07-04'
    ]
]
```

### Working with Provinces
```php
// Get all provinces
$provinces = $client->provinces()->all();

// Find province by code
$province = $client->provinces()->find('31');

// Find province by name
$province = $client->provinces()->findByName('Jakarta');
// Returns: ['code' => '31', 'name' => 'DKI Jakarta']

// Search provinces (returns multiple results)
$provinces = $client->provinces()->search('jawa');
// Returns all provinces containing "jawa" in their name
```

### Working with Regencies
```php
// Get regencies in a province
$regencies = $client->regencies()->byProvince('31');

// Find regency by code
$regency = $client->regencies()->find('31', '31.74');

// Find regency by name within a province
$regency = $client->regencies()->findByName('31', 'Jakarta Selatan');

// Find regency by name across all provinces (slower)
$regency = $client->regencies()->findByNameGlobal('Bandung');

// Search regencies within a province
$regencies = $client->regencies()->search('31', 'jakarta');
```

### Working with Districts
```php
// Get districts in a regency
$districts = $client->districts()->byRegency('31.74');

// Find district by code
$district = $client->districts()->find('31.74', '31.74.09');

// Find district by name
$district = $client->districts()->findByName('31.74', 'Jagakarsa');

// Search districts within a regency
$districts = $client->districts()->search('31.74', 'cilandak');
```

### Working with Villages
```php
// Get villages in a district
$villages = $client->villages()->byDistrict('31.74.09');

// Find village by code
$village = $client->villages()->find('31.74.09', '31.74.09.1001');

// Find village by name
$village = $client->villages()->findByName('31.74.09', 'Cipedak');

// Search villages within a district
$villages = $client->villages()->search('31.74.09', 'raya');
```

### Fluent Chaining Example
```php
// Find a complete address hierarchy
$client = new Client();

// Start from province
$province = $client->provinces()->findByName('Jakarta');

if ($province) {
    // Get regency within that province
    $regency = $client->regencies()->findByName($province['code'], 'Jakarta Selatan');
    
    if ($regency) {
        // Get district within that regency
        $district = $client->districts()->findByName($regency['code'], 'Jagakarsa');
        
        if ($district) {
            // Get all villages in that district
            $villages = $client->villages()->byDistrict($district['code']);
        }
    }
}
```

### Setting Timeout
```php
// Set timeout in constructor (default is 30 seconds)
$client = new Client(60);

// Or use setter method
$client->setTimeout(60);
```

## API Reference

### Client Methods
- `provinces(): Province` - Get Province resource
- `regencies(): Regency` - Get Regency resource
- `districts(): District` - Get District resource
- `villages(): Village` - Get Village resource
- `setTimeout(int $timeout): self` - Set request timeout

### Province Resource
- `all(): array` - Get all provinces
- `find(string $code): ?array` - Find province by code
- `findByName(string $name): ?array` - Find province by name
- `search(string $name): array` - Search provinces by name pattern

### Regency Resource
- `byProvince(string $provinceCode): array` - Get regencies by province
- `find(string $provinceCode, string $code): ?array` - Find regency by code
- `findByName(string $provinceCode, string $name): ?array` - Find regency by name
- `findByNameGlobal(string $name): ?array` - Find regency across all provinces
- `search(string $provinceCode, string $name): array` - Search regencies

### District Resource
- `byRegency(string $regencyCode): array` - Get districts by regency
- `find(string $regencyCode, string $code): ?array` - Find district by code
- `findByName(string $regencyCode, string $name): ?array` - Find district by name
- `search(string $regencyCode, string $name): array` - Search districts

### Village Resource
- `byDistrict(string $districtCode): array` - Get villages by district
- `find(string $districtCode, string $code): ?array` - Find village by code
- `findByName(string $districtCode, string $name): ?array` - Find village by name
- `search(string $districtCode, string $name): array` - Search villages

## Error Handling
The library throws `WilayahException` when an error occurs. Always wrap your API calls in try-catch blocks:

```php
use Silalahi\Wilayah\Exceptions\WilayahException;

try {
    $provinces = $client->provinces()->all();
} catch (WilayahException $e) {
    // Handle the error
    error_log($e->getMessage());
}
```

## Directory Structure
```
src/
├── Client.php                 # Main client class
├── Exceptions/
│   └── WilayahException.php   # Custom exception
└── Resources/
    ├── BaseResource.php       # Base resource class
    ├── Province.php           # Province resource
    ├── Regency.php            # Regency resource
    ├── District.php           # District resource
    └── Village.php            # Village resource
```

## Testing
```bash
composer test
```

## Contributing
Contributions are welcome! Please feel free to submit a Pull Request.

## License
This package is open-sourced software licensed under the [MIT license](LICENSE).