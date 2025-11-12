<?php

namespace Silalahi\Wilayah\Tests;

use PHPUnit\Framework\TestCase;
use Silalahi\Wilayah\Client;
use Silalahi\Wilayah\Exceptions\WilayahException;

class ClientTest extends TestCase
{
    private Client $wilayah;

    protected function setUp(): void
    {
        $this->wilayah = new Client();
    }

    // Province Tests
    public function testGetAllProvinces(): void
    {
        $result = $this->wilayah->provinces()->all();

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
        $result = $this->wilayah->provinces()->find('31');

        $this->assertIsArray($result);
        $this->assertEquals('31', $result['code']);
        $this->assertStringContainsString('Jakarta', $result['name']);
    }

    public function testFindProvinceByName(): void
    {
        $result = $this->wilayah->provinces()->findByName('Jakarta');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('code', $result);
        $this->assertEquals('31', $result['code']);
    }

    public function testSearchProvinces(): void
    {
        $results = $this->wilayah->provinces()->search('jawa');

        $this->assertIsArray($results);
        $this->assertNotEmpty($results);

        foreach ($results as $province) {
            $this->assertStringContainsStringIgnoringCase('jawa', $province['name']);
        }
    }

    // Regency Tests
    public function testGetRegenciesByProvince(): void
    {
        $result = $this->wilayah->regencies()->byProvince('31');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertNotEmpty($result['data']);
    }

    public function testFindRegencyByCode(): void
    {
        $result = $this->wilayah->regencies()->find('31', '31.74');

        $this->assertIsArray($result);
        $this->assertEquals('31.74', $result['code']);
    }

    public function testFindRegencyByName(): void
    {
        $result = $this->wilayah->regencies()->findByName('31', 'Jakarta Selatan');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('code', $result);
        $this->assertStringContainsString('Jakarta Selatan', $result['name']);
    }

    public function testFindRegencyByNameGlobal(): void
    {
        $result = $this->wilayah->regencies()->findByNameGlobal('Jakarta Selatan');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('code', $result);
    }

    public function testSearchRegencies(): void
    {
        $results = $this->wilayah->regencies()->search('31', 'jakarta');

        $this->assertIsArray($results);
    }

    // District Tests
    public function testGetDistrictsByRegency(): void
    {
        $result = $this->wilayah->districts()->byRegency('31.74');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertNotEmpty($result['data']);
    }

    public function testFindDistrictByCode(): void
    {
        $result = $this->wilayah->districts()->find('31.74', '31.74.09');

        $this->assertIsArray($result);
        $this->assertEquals('31.74.09', $result['code']);
    }

    public function testFindDistrictByName(): void
    {
        $result = $this->wilayah->districts()->findByName('31.74', 'Jagakarsa');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('code', $result);
        $this->assertStringContainsString('Jagakarsa', $result['name']);
    }

    public function testSearchDistricts(): void
    {
        $results = $this->wilayah->districts()->search('31.74', 'a');

        $this->assertIsArray($results);
    }

    // Village Tests
    public function testGetVillagesByDistrict(): void
    {
        $result = $this->wilayah->villages()->byDistrict('31.74.09');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertNotEmpty($result['data']);
    }

    public function testFindVillageByName(): void
    {
        $villages = $this->wilayah->villages()->byDistrict('31.74.09');

        if (!empty($villages['data'])) {
            $firstVillage = $villages['data'][0];
            $result = $this->wilayah->villages()->findByName('31.74.09', $firstVillage['name']);

            $this->assertIsArray($result);
            $this->assertEquals($firstVillage['code'], $result['code']);
        }
    }

    public function testSearchVillages(): void
    {
        $results = $this->wilayah->villages()->search('31.74.09', 'a');

        $this->assertIsArray($results);
    }

    // General Tests
    public function testSetTimeout(): void
    {
        $wilayah = $this->wilayah->setTimeout(60);

        $this->assertInstanceOf(Client::class, $wilayah);
        $this->assertEquals(60, $wilayah->getTimeout());
    }

    public function testInvalidProvinceCode(): void
    {
        $this->expectException(WilayahException::class);

        $this->wilayah->regencies()->byProvince('999999');
    }

    public function testCaseInsensitiveSearch(): void
    {
        $lowercase = $this->wilayah->provinces()->findByName('jakarta');
        $uppercase = $this->wilayah->provinces()->findByName('JAKARTA');
        $mixed = $this->wilayah->provinces()->findByName('JaKaRtA');

        $this->assertEquals($lowercase, $uppercase);
        $this->assertEquals($lowercase, $mixed);
    }

    public function testNotFoundReturnsNull(): void
    {
        $result = $this->wilayah->provinces()->findByName('NonExistentProvince123');

        $this->assertNull($result);
    }

    public function testResourceInstances(): void
    {
        $this->assertInstanceOf(
            \Silalahi\Wilayah\Resources\Province::class,
            $this->wilayah->provinces()
        );

        $this->assertInstanceOf(
            \Silalahi\Wilayah\Resources\Regency::class,
            $this->wilayah->regencies()
        );

        $this->assertInstanceOf(
            \Silalahi\Wilayah\Resources\District::class,
            $this->wilayah->districts()
        );

        $this->assertInstanceOf(
            \Silalahi\Wilayah\Resources\Village::class,
            $this->wilayah->villages()
        );
    }
}