<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\ClientAble;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

/**
 * @mixin Client
 */
class Client
{
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
     *  Content-Type.
     * @var string
     */
    protected $contentType = 'application/json';

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
     * Accept.
     * @var string
     */
    protected $accept = 'application/json';

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
    public function restRequest(string $method, string $uri, array $options): ResponseInterface
    {
        $options = $this->getClientRequestOptions($options);
        return $this->request($method, $uri, $options);
    }

    /**
     * send the request.
     */
    public function fileRequest(string $method, string $uri, array $options): ResponseInterface
    {
        $this->setContentType('');
        $this->setAccept('');
        $options = $this->getClientRequestOptions($options);
        return $this->request($method, $uri, $options);
    }

    /**
     * get the request options.
     */
    public function getClientRequestOptions(array $options): array
    {
        $baseUri = $this->getBaseUri();
        // 需要特殊判断一下后缀，必须以 / 结尾，否则作为 http base_uri 会被 Guzzle 丢弃最后一个 / 后面的所有参数
        if (substr($baseUri, -1, 1) !== '/') {
            $baseUri .= '/';
        }
        $headers = $this->getHeaders();
        if ($this->getContentType()) {
            $headers['Content-Type'] = $this->getContentType();
        }
        if ($this->getAccept()) {
            $headers['Accept'] = $this->getAccept();
        }
        if ($this->getUserAgent()) {
            $headers['User-Agent'] = $this->getUserAgent();
        }
        if ($this->getAuthorization()) {
            $headers['Authorization'] = $this->getAuthorization();
        }
        return array_merge([
            'base_uri' => $baseUri,
            'connect_timeout' => $this->getConnectTimeout(),
            'read_timeout' => $this->getReadTimeout(),
            'timeout' => $this->getTimeout(),
            'headers' => $headers,
        ], $options);
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
     * @return self
     */
    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;

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

    private function request(string $method, string $uri, array $options)
    {
        return (new GuzzleClient())->request($method, $uri, $options);
    }
}
