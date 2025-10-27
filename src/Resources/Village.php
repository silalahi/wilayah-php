<?php

namespace Silalahi\Wilayah\Resources;

use Silalahi\Wilayah\Exceptions\WilayahException;

/**
 * Village Resource
 *
 * Handles operations related to villages (kelurahan/desa)
 *
 * @package Silalahi\Wilayah\Resources
 */
class Village extends BaseResource
{
    /**
     * Get villages by district code
     *
     * @param string $districtCode District code (e.g., "31.74.09")
     * @return array
     * @throws WilayahException
     */
    public function byDistrict(string $districtCode): array
    {
        return $this->client->request("/villages/{$districtCode}.json");
    }

    /**
     * Find village by code
     *
     * @param string $districtCode District code
     * @param string $code Village code (e.g., "31.74.09.1001")
     * @return array|null
     * @throws WilayahException
     */
    public function find(string $districtCode, string $code): ?array
    {
        $villages = $this->byDistrict($districtCode);

        foreach ($villages['data'] as $village) {
            if ($village['code'] === $code) {
                return $village;
            }
        }

        return null;
    }

    /**
     * Find village by name within a district
     *
     * @param string $districtCode District code
     * @param string $name Village name (case-insensitive, partial match)
     * @return array|null
     * @throws WilayahException
     */
    public function findByName(string $districtCode, string $name): ?array
    {
        $villages = $this->byDistrict($districtCode);
        return $this->searchByName($villages['data'], $name);
    }

    /**
     * Search villages by name within a district
     *
     * @param string $districtCode District code
     * @param string $name Village name or pattern (case-insensitive)
     * @return array
     * @throws WilayahException
     */
    public function search(string $districtCode, string $name): array
    {
        $villages = $this->byDistrict($districtCode);
        return $this->searchAllByName($villages['data'], $name);
    }
}