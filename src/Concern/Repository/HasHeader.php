<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\ClientAble\Concern\Repository;

use Tusimo\ClientAble\Resolver\DefaultHeaderResolver;
use Tusimo\ClientAble\Contract\HeaderResolverContract;

trait HasHeader
{
    protected $headers = [];

    /**
     * @var HeaderResolverContract
     */
    protected $headerResolver;

    /**
     * set request context in headers.
     */
    public function setRequestContext(string $key, string $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * set current user.
     *
     * @param string $userId
     * @return static
     */
    public function asUserId($userId)
    {
        return $this->setRequestContext('X-User-ID', $userId);
    }

    /**
     * set current consumer.
     *
     * @return static
     */
    public function asConsumer(string $consumer)
    {
        return $this->setRequestContext('X-Consumer-Name', $consumer);
    }

    /**
     * set current app.
     *
     * @return static
     */
    public function asApp(string $app)
    {
        return $this->setRequestContext('X-App', $app);
    }

    /**
     * set current language.
     *
     * @return static
     */
    public function asLanguage(string $language)
    {
        return $this->setRequestContext('X-Language', $language);
    }

    public function getHeaders(): array
    {
        return array_merge($this->getHeaderResolver()->getHeaders(), $this->headers);
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Undocumented function.
     * @return static
     */
    public function withHeader(string $header, string $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    public function getHeaderResolver(): HeaderResolverContract
    {
        if (is_null($this->headerResolver)) {
            $this->headerResolver = new DefaultHeaderResolver();
        }
        return $this->headerResolver;
    }

    public function setHeaderResolver(HeaderResolverContract $headerResolver)
    {
        $this->headerResolver = $headerResolver;
    }
}
