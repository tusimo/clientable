<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Clientable;

class Service
{
    /**
     * service name.
     *
     * @var string
     */
    private $name;

    /**
     * Endpoint.
     *
     * @var string
     */
    private $endpoint;

    public function __construct(string $name, string $endpoint)
    {
        $this->name = $name;
        $this->endpoint = $endpoint;
    }

    /**
     * Get endpoint.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Set endpoint.
     *
     * @param string $endpoint Endpoint
     *
     * @return self
     */
    public function setEndpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Get service name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set service name.
     *
     * @param string $name service name
     *
     * @return self
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
}
