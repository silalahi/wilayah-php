<?php

namespace Silalahi\Wilayah\Resources;

use Silalahi\Wilayah\Client;

/**
 * Base Resource Class
 *
 * @package Silalahi\Wilayah\Resources
 */
abstract class BaseResource
{
    /**
     * @var Client
     */
    protected Client $client;

    /**
     * Constructor
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Search for an item by name in a data array
     *
     * @param array $data Array of items with 'name' key
     * @param string $searchName Name to search for (case-insensitive)
     * @return array|null Returns the first matching item or null
     */
    protected function searchByName(array $data, string $searchName): ?array
    {
        $searchName = mb_strtolower(trim($searchName));

        foreach ($data as $item) {
            $itemName = mb_strtolower($item['name']);

            // Exact match
            if ($itemName === $searchName) {
                return $item;
            }

            // Partial match
            if (strpos($itemName, $searchName) !== false) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Search for all items matching a name pattern
     *
     * @param array $data Array of items with 'name' key
     * @param string $searchName Name to search for (case-insensitive)
     * @return array Returns array of matching items
     */
    protected function searchAllByName(array $data, string $searchName): array
    {
        $searchName = mb_strtolower(trim($searchName));
        $results = [];

        foreach ($data as $item) {
            $itemName = mb_strtolower($item['name']);

            if ($itemName === $searchName || strpos($itemName, $searchName) !== false) {
                $results[] = $item;
            }
        }

        return $results;
    }
}