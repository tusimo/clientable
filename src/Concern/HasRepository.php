<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\ClientAble\Concern;

use Tusimo\ClientAble\Api;

trait HasRepository
{
    /**
     * User Agent.
     *
     * @var string
     */
    protected $userAgent = 'api.client v1.0';

    /**
     * Authorization.
     * @var string
     */
    protected $authorization = 'b9ddbadb4ebbf06110b93d98adb1497c';

    /**
     * Seconds before connect to the client.
     */
    protected $connectTimeout = 2.0;

    /**
     * Seconds before request send back.
     */
    protected $timeout = 6.0;

    /**
     * Seconds for read.
     * @var float
     */
    protected $readTimeout = 3.0;

    /**
     * Debug Modal.
     */
    protected $debug = false;

    /**
     * @var string
     */
    protected $service;

    /**
     * resource name.
     *
     * @var string
     */
    protected $resource;

    /**
     * Protocol version.
     * This property represents the protocol of the api format version.
     *
     * @var string
     */
    protected $version = 'v2';

    /**
     * Api Version.
     * This property represents the api version.
     * @var string
     */
    protected $apiVersion = 'v2';

    /**
     * @var array
     */
    protected $with = [];

    /**
     * @var array
     */
    protected $select = [];

    public function resource($resource)
    {
        return $this->setResource($resource);
    }

    /**
     * Get the value of resourceName.
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set the value of resourceName.
     *
     * @param mixed $resource
     * @return self
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get the value of version.
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the value of version.
     *
     * @param mixed $version
     * @return self
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * check the version is target version.
     */
    public function isVersion(string $version): bool
    {
        return $this->version === $version;
    }

    /**
     * Get this property represents the api version.
     *
     * @return string
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * Set this property represents the api version.
     *
     * @param string $apiVersion this property represents the api version
     *
     * @return self
     */
    public function setApiVersion(string $apiVersion)
    {
        $this->apiVersion = $apiVersion;

        return $this;
    }

    /**
     * Get the value of connectTimeout.
     */
    public function getConnectTimeout()
    {
        if ($this->getDebug()) {
            return 0;
        }
        return $this->connectTimeout;
    }

    /**
     * Set the value of connectTimeout.
     *
     * @param mixed $connectTimeout
     * @return self
     */
    public function setConnectTimeout($connectTimeout)
    {
        $this->connectTimeout = $connectTimeout;

        return $this;
    }

    /**
     * Get the value of timeout.
     */
    public function getTimeout()
    {
        if ($this->getDebug()) {
            return 0;
        }
        return $this->timeout;
    }

    /**
     * Set the value of timeout.
     *
     * @param mixed $timeout
     * @return self
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Get the value of debug.
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Set the value of debug.
     *
     * @param mixed $debug
     * @return self
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;

        return $this;
    }

    public function getReadTimeout(): float
    {
        if ($this->getDebug()) {
            return 0;
        }
        return $this->readTimeout;
    }

    public function setReadTimeout(float $readTimeout)
    {
        $this->readTimeout = $readTimeout;
        return $this;
    }

    /**
     * Get user Agent.
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set user Agent.
     *
     * @param string $userAgent User Agent
     *
     * @return self
     */
    public function setUserAgent(string $userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Get authorization.
     *
     * @return string
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * Set authorization.
     *
     * @param string $authorization Authorization
     *
     * @return self
     */
    public function setAuthorization(string $authorization)
    {
        $this->authorization = $authorization;

        return $this;
    }

    /**
     * @return string
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * @param string $service
     */
    public function setService(string $service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * @return Api
     */
    public function newApi()
    {
        $api = Api::service($this->service);
        $api->setUserAgent($this->getUserAgent())
            ->setAuthorization($this->getAuthorization())
            ->setConnectTimeout($this->getConnectTimeout())
            ->setTimeout($this->getConnectTimeout())
            ->setReadTimeout($this->getReadTimeout())
            ->setDebug($this->getDebug())
            ->setResource($this->getResource())
            ->setVersion($this->getVersion())
            ->setApiVersion($this->getApiVersion());
        if ($this->select) {
            $api->select($this->select);
        }
        if ($this->with) {
            $api->with($this->with);
        }
        return $api;
    }
}
