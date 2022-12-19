<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\ClientAble\Concern\Repository;

use Psr\Http\Message\ResponseInterface;

trait HasRequest
{
    use HasClient;
    use HasHeader;

    /**
     * get the request options.
     */
    public function getClientRequestOptions(array $options): array
    {
        $baseUri = $this->getBaseUri();
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
     * send the request.
     */
    public function restRequest(string $method, string $uri, array $options): ResponseInterface
    {
        return $this->sendApiRequest($method, $uri, $options);
    }

    /**
     * send the request.
     */
    public function fileRequest(string $method, string $uri, array $options): ResponseInterface
    {
        $repo = clone $this;
        $repo->setContentType('');
        $repo->setAccept('');
        return $repo->sendApiRequest($method, $uri, $options);
    }

    public function sendApiRequest(string $method, string $uri, array $options): ResponseInterface
    {
        $options = $this->getClientRequestOptions($options);
        return $this->sendClientRequest($method, $uri, $options);
    }

    /**
     * Send the original guzzle request.
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendClientRequest(string $method, string $uri, array $options): ResponseInterface
    {
        return $this->getClient()->request($method, $uri, $options);
    }
}
