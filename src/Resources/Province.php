<?php

namespace Silalahi\Wilayah\Resources;

use Silalahi\Wilayah\Exceptions\WilayahException;

/**
 * Province Resource
 *
 * Handles operations related to provinces
 *
 * @package Silalahi\Wilayah\Resources
 */
class Province extends BaseResource
{
    /**
     * Get all provinces in Indonesia
     *
     * @return array
     * @throws WilayahException
     */
    public function all(): array
    {
        return $this->client->request('/provinces.json');
    }

    /**
     * Find province by code
     *
     * @param string $code Province code (e.g., "31")
     * @return array|null
     * @throws WilayahException
     */
    public function find(string $code): ?array
    {
        $provinces = $this->all();

        foreach ($provinces['data'] as $province) {
            if ($province['code'] === $code) {
                return $province;
            }
        }

        return null;
    }

    /**
     * Find province by name
     *
     * @param string $name Province name (case-insensitive, partial match)
     * @return array|null
     * @throws WilayahException
     */
    public function findByName(string $name): ?array
    {
        $provinces = $this->all();
        return $this->searchByName($provinces['data'], $name);
    }

    /**
     * Search provinces by name pattern
     *
     * @param string $name Province name or pattern (case-insensitive)
     * @return array
     * @throws WilayahException
     */
    public function search(string $name): array
    {
        $provinces = $this->all();
        return $this->searchAllByName($provinces['data'], $name);
    }
}