<?php

namespace Silalahi\Wilayah\Resources;

use Silalahi\Wilayah\Exceptions\WilayahException;

/**
 * Regency Resource
 *
 * Handles operations related to regencies (cities/kabupaten)
 *
 * @package Silalahi\Wilayah\Resources
 */
class Regency extends BaseResource
{
    /**
     * Get regencies by province code
     *
     * @param string $provinceCode Province code (e.g., "31")
     * @return array
     * @throws WilayahException
     */
    public function byProvince(string $provinceCode): array
    {
        return $this->client->request("/regencies/{$provinceCode}.json");
    }

    /**
     * Find regency by code
     *
     * @param string $provinceCode Province code
     * @param string $code Regency code (e.g., "31.74")
     * @return array|null
     * @throws WilayahException
     */
    public function find(string $provinceCode, string $code): ?array
    {
        $regencies = $this->byProvince($provinceCode);

        foreach ($regencies['data'] as $regency) {
            if ($regency['code'] === $code) {
                return $regency;
            }
        }

        return null;
    }

    /**
     * Find regency by name within a province
     *
     * @param string $provinceCode Province code
     * @param string $name Regency name (case-insensitive, partial match)
     * @return array|null
     * @throws WilayahException
     */
    public function findByName(string $provinceCode, string $name): ?array
    {
        $regencies = $this->byProvince($provinceCode);
        return $this->searchByName($regencies['data'], $name);
    }

    /**
     * Find regency by name across all provinces
     *
     * @param string $name Regency name (case-insensitive, partial match)
     * @return array|null
     * @throws WilayahException
     */
    public function findByNameGlobal(string $name): ?array
    {
        $provinceResource = new Province($this->client);
        $provinces = $provinceResource->all();

        foreach ($provinces['data'] as $province) {
            $regencies = $this->byProvince($province['code']);
            $found = $this->searchByName($regencies['data'], $name);

            if ($found !== null) {
                return $found;
            }
        }

        return null;
    }

    /**
     * Search regencies by name within a province
     *
     * @param string $provinceCode Province code
     * @param string $name Regency name or pattern (case-insensitive)
     * @return array
     * @throws WilayahException
     */
    public function search(string $provinceCode, string $name): array
    {
        $regencies = $this->byProvince($provinceCode);
        return $this->searchAllByName($regencies['data'], $name);
    }
}