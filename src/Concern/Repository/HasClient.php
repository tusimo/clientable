<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\ClientAble\Concern\Repository;

use GuzzleHttp\Client;
use Tusimo\ClientAble\Resolver\DefaultClientResolver;
use Tusimo\ClientAble\Contract\ClientResolverContract;

trait HasClient
{
    /**
     * Undocumented variable.
     *
     * @var string
     */
    protected $baseUri;

    /**
     *  Content-Type.
     * @var string
     */
    protected $contentType = 'application/json';

    /**
     * Accept.
     * @var string
     */
    protected $accept = 'application/json';

    /**
     * @var ClientResolverContract
     */
    protected $clientResolver;

    /**
     * Set ClientResolver.
     *
     * @param null|mixed $clientResolver
     * @return static
     */
    public function setClientResolver($clientResolver = null)
    {
        $this->clientResolver = $clientResolver;
        return $this;
    }

    /**
     * Get a client.
     * @return Client
     */
    public function getClient()
    {
        return $this->getClientResolver()->getClient();
    }

    /**
     * Get the value of clientResolver.
     */
    public function getClientResolver()
    {
        if (is_null($this->clientResolver)) {
            $clientResolver = new DefaultClientResolver();
            $this->clientResolver = $clientResolver;
        }
        return $this->clientResolver;
    }

    /**
     * Get the value of baseUri.
     * @return string
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * Set the value of baseUri.
     *
     * @param mixed $baseUri
     * @return static
     */
    public function setBaseUri($baseUri)
    {
        $this->baseUri = $baseUri;

        return $this;
    }

    /**
     * Get content-Type.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set content-Type.
     *
     * @param string $contentType Content-Type
     *
     * @return static
     */
    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Get accept.
     *
     * @return string
     */
    public function getAccept()
    {
        return $this->accept;
    }

    /**
     * Set accept.
     *
     * @param string $accept Accept
     *
     * @return self
     */
    public function setAccept(string $accept)
    {
        $this->accept = $accept;

        return $this;
    }
}
