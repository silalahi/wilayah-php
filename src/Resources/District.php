<?php

namespace Silalahi\Wilayah\Resources;

use Silalahi\Wilayah\Exceptions\WilayahException;

/**
 * District Resource
 *
 * Handles operations related to districts (kecamatan)
 *
 * @package Silalahi\Wilayah\Resources
 */
class District extends BaseResource
{
    /**
     * Get districts by regency code
     *
     * @param string $regencyCode Regency code (e.g., "31.74")
     * @return array
     * @throws WilayahException
     */
    public function byRegency(string $regencyCode): array
    {
        return $this->client->request("/districts/{$regencyCode}.json");
    }

    /**
     * Find district by code
     *
     * @param string $regencyCode Regency code
     * @param string $code District code (e.g., "31.74.09")
     * @return array|null
     * @throws WilayahException
     */
    public function find(string $regencyCode, string $code): ?array
    {
        $districts = $this->byRegency($regencyCode);

        foreach ($districts['data'] as $district) {
            if ($district['code'] === $code) {
                return $district;
            }
        }

        return null;
    }

    /**
     * Find district by name within a regency
     *
     * @param string $regencyCode Regency code
     * @param string $name District name (case-insensitive, partial match)
     * @return array|null
     * @throws WilayahException
     */
    public function findByName(string $regencyCode, string $name): ?array
    {
        $districts = $this->byRegency($regencyCode);
        return $this->searchByName($districts['data'], $name);
    }

    /**
     * Search districts by name within a regency
     *
     * @param string $regencyCode Regency code
     * @param string $name District name or pattern (case-insensitive)
     * @return array
     * @throws WilayahException
     */
    public function search(string $regencyCode, string $name): array
    {
        $districts = $this->byRegency($regencyCode);
        return $this->searchAllByName($districts['data'], $name);
    }
}