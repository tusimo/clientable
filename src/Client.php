<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Clientable;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

/**
 * @mixin Client
 */
class Client
{
    /**
     * Client.
     *
     * @var ?GuzzleClient
     */
    protected $client;

    /**
     * Undocumented variable.
     *
     * @var string
     */
    protected $baseUri;

    /**
     * Timeout in seconds.
     * @var float
     */
    protected $timeout = 20;

    /**
     * Connect timeout in seconds.
     * @var float
     */
    protected $connectTimeout = 2;

    /**
     * Read Timeout in seconds.
     * @var float
     */
    protected $readTimeout = 10;

    /**
     * Headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * __Call.
     *
     * @param string $method
     * @param array $parameters
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this->getClient(), $method)) {
            $result = $this->getClient()->{$method}(...$parameters);
            if ($result instanceof GuzzleClient) {
                return $this;
            }
            return $result;
        }
        throw new \Exception('unsupported method call:' . $method);
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
     * @return self
     */
    public function setBaseUri($baseUri)
    {
        $this->baseUri = $baseUri;

        return $this;
    }

    /**
     * Set client.
     *
     * @param GuzzleClient $client Client
     *
     * @return self
     */
    public function setClient(GuzzleClient $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get the value of headers.
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set the value of headers.
     *
     * @param mixed $headers
     * @return self
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
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

    /**
     * Get timeout in seconds.
     * @return float
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set timeout in seconds.
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
     * Get connect timeout in seconds.
     * @return float
     */
    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    /**
     * Set connect timeout in seconds.
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
     * Get read Timeout in seconds.
     * @return float
     */
    public function getReadTimeout()
    {
        return $this->readTimeout;
    }

    /**
     * Set read Timeout in seconds.
     *
     * @param mixed $readTimeout
     * @return self
     */
    public function setReadTimeout($readTimeout)
    {
        $this->readTimeout = $readTimeout;

        return $this;
    }

    /**
     * send the request.
     */
    public function request(string $method, string $uri, array $options): ResponseInterface
    {
        $options = array_merge($this->getClientRequestOptions(), $options);
        return $this->getClient()
            ->request($method, $uri, $options);
    }

    /**
     * get the request options.
     */
    public function getClientRequestOptions(): array
    {
        $baseUri = $this->getBaseUri();
        // 需要特殊判断一下后缀，必须以 / 结尾，否则作为 http base_uri 会被 Guzzle 丢弃最后一个 / 后面的所有参数
        if (substr($baseUri, -1, 1) !== '/') {
            $baseUri .= '/';
        }
        return [
            'base_uri' => $baseUri,
            'connect_timeout' => $this->getConnectTimeout(),
            'read_timeout' => $this->getReadTimeout(),
            'timeout' => $this->getTimeout(),
            'headers' => $this->getHeaders() + [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'api.client v1.0',
                'Authorization' => 'b9ddbadb4ebbf06110b93d98adb1497c',
            ],
        ];
    }

    /**
     * Undocumented function.
     *
     * @return GuzzleClient
     */
    protected function getClient()
    {
        if (is_null($this->client)) {
            $this->client = new GuzzleClient($this->getClientRequestOptions());
        }
        return $this->client;
    }
}
