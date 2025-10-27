<?php

require __DIR__ . '/../vendor/autoload.php';

use Silalahi\Wilayah\Client;
use Silalahi\Wilayah\Exceptions\WilayahException;

// Create client instance
$client = new Client();

echo "=== Wilayah PHP Client Examples ===\n\n";

try {
    // Example 1: Get all provinces
    echo "1. Getting all provinces:\n";
    $provinces = $client->provinces()->all();
    echo "   Found " . count($provinces['data']) . " provinces\n";
    echo "   First province: {$provinces['data'][0]['name']} (Code: {$provinces['data'][0]['code']})\n\n";

    // Example 2: Find province by name
    echo "2. Finding province by name:\n";
    $jakarta = $client->provinces()->findByName('Jakarta');
    if ($jakarta) {
        echo "   Found: {$jakarta['name']} (Code: {$jakarta['code']})\n\n";
    }

    // Example 3: Search provinces
    echo "3. Searching provinces with 'jawa':\n";
    $javaProvinces = $client->provinces()->search('jawa');
    foreach ($javaProvinces as $province) {
        echo "   - {$province['name']} (Code: {$province['code']})\n";
    }
    echo "\n";

    // Example 4: Get regencies in a province
    echo "4. Getting regencies in DKI Jakarta (Code: 31):\n";
    $jakartaRegencies = $client->regencies()->byProvince('31');
    echo "   Found " . count($jakartaRegencies['data']) . " regencies\n";
    foreach ($jakartaRegencies['data'] as $regency) {
        echo "   - {$regency['name']} (Code: {$regency['code']})\n";
    }
    echo "\n";

    // Example 5: Find regency by name
    echo "5. Finding regency by name:\n";
    $jakartaSelatan = $client->regencies()->findByName('31', 'Jakarta Selatan');
    if ($jakartaSelatan) {
        echo "   Found: {$jakartaSelatan['name']} (Code: {$jakartaSelatan['code']})\n\n";
    }

    // Example 6: Find regency globally (without knowing province)
    echo "6. Finding regency globally (Bandung):\n";
    $bandung = $client->regencies()->findByNameGlobal('Bandung');
    if ($bandung) {
        echo "   Found: {$bandung['name']} (Code: {$bandung['code']})\n\n";
    }

    // Example 7: Get districts in a regency
    echo "7. Getting districts in Jakarta Selatan (Code: 31.74):\n";
    $districts = $client->districts()->byRegency('31.74');
    echo "   Found " . count($districts['data']) . " districts\n";
    echo "   First 5 districts:\n";
    foreach (array_slice($districts['data'], 0, 5) as $district) {
        echo "   - {$district['name']} (Code: {$district['code']})\n";
    }
    echo "\n";

    // Example 8: Find district by name
    echo "8. Finding district by name:\n";
    $jagakarsa = $client->districts()->findByName('31.74', 'Jagakarsa');
    if ($jagakarsa) {
        echo "   Found: {$jagakarsa['name']} (Code: {$jagakarsa['code']})\n\n";
    }

    // Example 9: Get villages in a district
    echo "9. Getting villages in Jagakarsa (Code: 31.74.09):\n";
    $villages = $client->villages()->byDistrict('31.74.09');
    echo "   Found " . count($villages['data']) . " villages\n";
    echo "   First 5 villages:\n";
    foreach (array_slice($villages['data'], 0, 5) as $village) {
        echo "   - {$village['name']} (Code: {$village['code']})\n";
    }
    echo "\n";

    // Example 10: Complete address hierarchy
    echo "10. Building complete address hierarchy:\n";
    $province = $client->provinces()->findByName('Jakarta');

    if ($province) {
        echo "   Province: {$province['name']}\n";

        $regency = $client->regencies()->findByName($province['code'], 'Jakarta Selatan');
        if ($regency) {
            echo "   Regency: {$regency['name']}\n";

            $district = $client->districts()->findByName($regency['code'], 'Jagakarsa');
            if ($district) {
                echo "   District: {$district['name']}\n";

                $villagesData = $client->villages()->byDistrict($district['code']);
                if (!empty($villagesData['data'])) {
                    $village = $villagesData['data'][0];
                    echo "   Village: {$village['name']}\n";
                }
            }
        }
    }
    echo "\n";

    // Example 11: Case-insensitive search
    echo "11. Case-insensitive search:\n";
    $lower = $client->provinces()->findByName('jakarta');
    $upper = $client->provinces()->findByName('JAKARTA');
    $mixed = $client->provinces()->findByName('JaKaRtA');
    echo "   All searches return same result: " .
        ($lower['code'] === $upper['code'] && $upper['code'] === $mixed['code'] ? 'YES' : 'NO') . "\n\n";

    // Example 12: Handling not found
    echo "12. Handling not found:\n";
    $notFound = $client->provinces()->findByName('NonExistent');
    echo "   Result: " . ($notFound === null ? 'NULL (not found)' : 'Found') . "\n\n";

} catch (WilayahException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "=== Examples completed ===\n";