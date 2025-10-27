<?php

namespace Silalahi\Wilayah\Tests;

use PHPUnit\Framework\TestCase;
use Silalahi\Wilayah\Client;
use Silalahi\Wilayah\Exceptions\WilayahException;

class ClientTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client();
    }

    // Province Tests
    public function testGetAllProvinces(): void
    {
        $result = $this->client->provinces()->all();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertIsArray($result['data']);
        $this->assertNotEmpty($result['data']);

        $firstProvince = $result['data'][0];
        $this->assertArrayHasKey('code', $firstProvince);
        $this->assertArrayHasKey('name', $firstProvince);
    }

    public function testFindProvinceByCode(): void
    {
        $result = $this->client->provinces()->find('31');

        $this->assertIsArray($result);
        $this->assertEquals('31', $result['code']);
        $this->assertStringContainsString('Jakarta', $result['name']);
    }

    public function testFindProvinceByName(): void
    {
        $result = $this->client->provinces()->findByName('Jakarta');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('code', $result);
        $this->assertEquals('31', $result['code']);
    }

    public function testSearchProvinces(): void
    {
        $results = $this->client->provinces()->search('jawa');

        $this->assertIsArray($results);
        $this->assertNotEmpty($results);

        foreach ($results as $province) {
            $this->assertStringContainsStringIgnoringCase('jawa', $province['name']);
        }
    }

    // Regency Tests
    public function testGetRegenciesByProvince(): void
    {
        $result = $this->client->regencies()->byProvince('31');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertNotEmpty($result['data']);
    }

    public function testFindRegencyByCode(): void
    {
        $result = $this->client->regencies()->find('31', '31.74');

        $this->assertIsArray($result);
        $this->assertEquals('31.74', $result['code']);
    }

    public function testFindRegencyByName(): void
    {
        $result = $this->client->regencies()->findByName('31', 'Jakarta Selatan');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('code', $result);
        $this->assertStringContainsString('Jakarta Selatan', $result['name']);
    }

    public function testFindRegencyByNameGlobal(): void
    {
        $result = $this->client->regencies()->findByNameGlobal('Jakarta Selatan');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('code', $result);
    }

    public function testSearchRegencies(): void
    {
        $results = $this->client->regencies()->search('31', 'jakarta');

        $this->assertIsArray($results);
    }

    // District Tests
    public function testGetDistrictsByRegency(): void
    {
        $result = $this->client->districts()->byRegency('31.74');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertNotEmpty($result['data']);
    }

    public function testFindDistrictByCode(): void
    {
        $result = $this->client->districts()->find('31.74', '31.74.09');

        $this->assertIsArray($result);
        $this->assertEquals('31.74.09', $result['code']);
    }

    public function testFindDistrictByName(): void
    {
        $result = $this->client->districts()->findByName('31.74', 'Jagakarsa');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('code', $result);
        $this->assertStringContainsString('Jagakarsa', $result['name']);
    }

    public function testSearchDistricts(): void
    {
        $results = $this->client->districts()->search('31.74', 'a');

        $this->assertIsArray($results);
    }

    // Village Tests
    public function testGetVillagesByDistrict(): void
    {
        $result = $this->client->villages()->byDistrict('31.74.09');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertNotEmpty($result['data']);
    }

    public function testFindVillageByName(): void
    {
        $villages = $this->client->villages()->byDistrict('31.74.09');

        if (!empty($villages['data'])) {
            $firstVillage = $villages['data'][0];
            $result = $this->client->villages()->findByName('31.74.09', $firstVillage['name']);

            $this->assertIsArray($result);
            $this->assertEquals($firstVillage['code'], $result['code']);
        }
    }

    public function testSearchVillages(): void
    {
        $results = $this->client->villages()->search('31.74.09', 'a');

        $this->assertIsArray($results);
    }

    // General Tests
    public function testSetTimeout(): void
    {
        $client = $this->client->setTimeout(60);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals(60, $client->getTimeout());
    }

    public function testInvalidProvinceCode(): void
    {
        $this->expectException(WilayahException::class);

        $this->client->regencies()->byProvince('999999');
    }

    public function testCaseInsensitiveSearch(): void
    {
        $lowercase = $this->client->provinces()->findByName('jakarta');
        $uppercase = $this->client->provinces()->findByName('JAKARTA');
        $mixed = $this->client->provinces()->findByName('JaKaRtA');

        $this->assertEquals($lowercase, $uppercase);
        $this->assertEquals($lowercase, $mixed);
    }

    public function testNotFoundReturnsNull(): void
    {
        $result = $this->client->provinces()->findByName('NonExistentProvince123');

        $this->assertNull($result);
    }

    public function testResourceInstances(): void
    {
        $this->assertInstanceOf(
            \Silalahi\Wilayah\Resources\Province::class,
            $this->client->provinces()
        );

        $this->assertInstanceOf(
            \Silalahi\Wilayah\Resources\Regency::class,
            $this->client->regencies()
        );

        $this->assertInstanceOf(
            \Silalahi\Wilayah\Resources\District::class,
            $this->client->districts()
        );

        $this->assertInstanceOf(
            \Silalahi\Wilayah\Resources\Village::class,
            $this->client->villages()
        );
    }
}