<?php

namespace Silalahi\Wilayah;

use Silalahi\Wilayah\Resources\Province;
use Silalahi\Wilayah\Resources\Regency;
use Silalahi\Wilayah\Resources\District;
use Silalahi\Wilayah\Resources\Village;

/**
 * WilayahId API Client
 *
 * A PHP wrapper for wilayah.id API - Indonesian administrative regions data
 *
 * @package YourVendor\WilayahId
 * @author Your Name
 * @license MIT
 */
class Client
{
    /**
     * Base URL for wilayah.id API
     */
    private const BASE_URL = 'https://wilayah.id/api';

    /**
     * HTTP client timeout in seconds
     */
    private int $timeout;

    /**
     * Constructor
     *
     * @param int $timeout HTTP request timeout in seconds
     */
    public function __construct(int $timeout = 30)
    {
        $this->timeout = $timeout;
    }

    /**
     * Get Province resource instance
     *
     * @return Province
     */
    public function provinces(): Province
    {
        return new Province($this);
    }

    /**
     * Get Regency resource instance
     *
     * @return Regency
     */
    public function regencies(): Regency
    {
        return new Regency($this);
    }

    /**
     * Get District resource instance
     *
     * @return District
     */
    public function districts(): District
    {
        return new District($this);
    }

    /**
     * Get Village resource instance
     *
     * @return Village
     */
    public function villages(): Village
    {
        return new Village($this);
    }

    /**
     * Make HTTP request to wilayah.id API
     *
     * @param string $endpoint API endpoint path
     * @return array
     * @throws Exceptions\WilayahException
     */
    public function request(string $endpoint): array
    {
        $url = self::BASE_URL . $endpoint;

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => $this->timeout,
                'header' => 'User-Agent: WilayahId-PHP-Client/1.0',
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            $error = error_get_last();
            throw new Exceptions\WilayahException(
                "Failed to fetch data from wilayah.id: " . ($error['message'] ?? 'Unknown error')
            );
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exceptions\WilayahException(
                "Failed to parse JSON response: " . json_last_error_msg()
            );
        }

        return $data;
    }

    /**
     * Set request timeout
     *
     * @param int $timeout Timeout in seconds
     * @return self
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Get current timeout setting
     *
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }
}