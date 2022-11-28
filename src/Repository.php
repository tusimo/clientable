<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Clientable;

use Exception;
use Tusimo\Restable\Query;
use GuzzleHttp\Psr7\Response;
use Tusimo\Restable\QuerySelect;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

/**
 * @mixin Client
 */
class Repository
{
    /**
     * resource name.
     *
     * @var string
     */
    protected $resourceName;

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
     * Client.
     *
     * @var Client
     */
    protected $client;

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
            if ($result instanceof Client) {
                return $this;
            }
            return $result;
        }
        throw new Exception('unsupported method call:' . $method);
    }

    /**
     * Get the value of resourceName.
     * @return string
     */
    public function getResourceName()
    {
        return $this->resourceName;
    }

    /**
     * Set the value of resourceName.
     *
     * @param mixed $resourceName
     * @return self
     */
    public function setResourceName($resourceName)
    {
        $this->resourceName = $resourceName;

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
     * Get Resource by id.
     *
     * @param int|string $id
     */
    public function get($id, array $select = [], array $with = []): ApiResponse
    {
        $options = $this->getClient()->getClientRequestOptions();
        $uri = "{$this->getApiVersion()}/{$this->getResourceName()}/{$id}";
        $query = new Query();
        $queryString = $query->select($this->parseSelect($select))
            ->with($with)
            ->toUriQueryString($this->getVersion());

        $uri .= '?' . $queryString;
        return $this->request('GET', $uri, $options);
    }

    /**
     * Get Resources by Ids.
     */
    public function getByIds(array $ids, array $select = [], array $with = []): ApiResponse
    {
        $this->throwExceptionIfNotSupported();

        $options = $this->getClient()->getClientRequestOptions();
        $idsString = implode(',', $ids);
        $uri = "{$this->getApiVersion()}/{$this->getResourceName()}/{$idsString}/_batch";
        $query = new Query();
        $queryString = $query->select($this->parseSelect($select))
            ->with($with)
            ->toUriQueryString($this->getVersion());

        $uri .= '?' . $queryString;
        return $this->request('GET', $uri, $options);
    }

    /**
     * Add Resource and return Resource With Id.
     */
    public function add(array $resource): ApiResponse
    {
        $options = $this->getClient()->getClientRequestOptions();
        $uri = "{$this->getApiVersion()}/{$this->getResourceName()}";
        $options['json'] = $resource;
        return $this->request('POST', $uri, $options);
    }

    /**
     * Batch add resource.
     */
    public function batchAdd(array $resources): ApiResponse
    {
        $this->throwExceptionIfNotSupported();

        $options = $this->getClient()->getClientRequestOptions();
        $uri = "{$this->getApiVersion()}/{$this->getResourceName()}/_batch";
        $options['json'] = $resources;
        return $this->request('POST', $uri, $options);
    }

    /**
     * Update Resource.
     *
     * @param int|string $id
     */
    public function update($id, array $resource): ApiResponse
    {
        $options = $this->getClient()->getClientRequestOptions();
        $uri = "{$this->getApiVersion()}/{$this->getResourceName()}/{$id}";
        $options['json'] = $resource;
        return $this->request('PUT', $uri, $options);
    }

    /**
     * Batch Update Resource.
     */
    public function batchUpdate(array $resources): ApiResponse
    {
        $this->throwExceptionIfNotSupported();

        $options = $this->getClient()->getClientRequestOptions();
        $uri = "{$this->getApiVersion()}/{$this->getResourceName()}/_batch";
        $options['json'] = $resources;
        return $this->request('PUT', $uri, $options);
    }

    /**
     * Delete resource.
     *
     * @param int|string $id
     */
    public function delete($id): ApiResponse
    {
        $options = $this->getClient()->getClientRequestOptions();
        $uri = "{$this->getApiVersion()}/{$this->getResourceName()}/{$id}";
        return $this->request('DELETE', $uri, $options);
    }

    /**
     * Batch delete Resource.
     */
    public function deleteByIds(array $ids): ApiResponse
    {
        $this->throwExceptionIfNotSupported();

        $options = $this->getClient()->getClientRequestOptions();
        $idsString = implode(',', $ids);
        $uri = "{$this->getApiVersion()}/{$this->getResourceName()}/{$idsString}/_batch";
        return $this->request('DELETE', $uri, $options);
    }

    /**
     * Get Resource Paginator.
     */
    public function list(Query $query): ApiResponse
    {
        $query = $this->parseQuery($query);
        $options = $this->getClient()->getClientRequestOptions();
        $path = "{$this->getApiVersion()}/{$this->getResourceName()}";
        $queryString = $query->toUriQueryString($this->getVersion());
        $uri = $path . '?' . $queryString;
        return $this->request('GET', $uri, $options);
    }

    /**
     * Get Resource By Query.
     */
    public function getByQuery(Query $query): ApiResponse
    {
        $query = $this->parseQuery($query);

        $options = $this->getClient()->getClientRequestOptions();
        $path = "{$this->getApiVersion()}/{$this->getResourceName()}/_batch";
        $queryString = $query->toUriQueryString($this->getVersion());
        $uri = $path . '?' . $queryString;
        return $this->request('GET', $uri, $options);
    }

    /**
     * check the version is target version.
     */
    public function isVersion(string $version): bool
    {
        return $this->version === $version;
    }

    /**
     * Get Resource aggregate By Query.
     */
    public function aggregate(Query $query): ApiResponse
    {
        $query = $this->parseQuery($query);

        $options = $this->getClientRequestOptions();
        $path = "{$this->getApiVersion()}/{$this->getResourceName()}/_aggregate";
        $queryString = $query->toUriQueryString($this->getVersion());
        $uri = $path . '?' . $queryString;
        return $this->request('GET', $uri, $options);
    }

    /**
     * Get the value of client.
     */
    public function getClient()
    {
        if (is_null($this->client)) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * Set the value of client.
     *
     * @param mixed $client
     * @return self
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
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

    protected function throwExceptionIfNotSupported()
    {
        if ($this->isVersion('v1')) {
            throw new \RuntimeException('api not supported by version:' . $this->version);
        }
    }

    /**
     * 兼容接口.
     */
    protected function parseSelect(array $select = []): array
    {
        if (empty($select)) {
            return $select;
        }
        if (in_array('*', $select)) {
            return [];
        }
        return $select;
    }

    protected function parseQuery(Query $query): Query
    {
        if ($query->hasQuerySelect()) {
            $query->setQuerySelect(new QuerySelect($this->parseSelect($query->getQuerySelect()->getSelects())));
        }
        return $query;
    }

    /**
     * decode response.
     */
    protected function decodeResponse(ResponseInterface $response): array
    {
        $data = json_decode($response->getBody()->getContents(), true);
        if (is_array($data)) {
            return $data;
        }
        return [];
    }

    /**
     * send the request.
     */
    protected function request(string $method, string $uri, array $options): ApiResponse
    {
        try {
            $response = $this->getClient()
                ->request($method, $uri, $options);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        } catch (RequestException $e) {
            $response = $e->getResponse();
            if (is_null($response)) {
                $response = new Response(500, $this->getHeaders(), $e->getMessage());
            }
        } catch (ConnectException $e) {
            $response = $e->getResponse();
            if (is_null($response)) {
                $response = new Response(500, $this->getHeaders(), $e->getMessage());
            }
        } catch (Exception $e) {
            $data = json_encode([
                'code' => 400,
                'msg' => $e->getMessage(),
                'data' => [],
                'meta' => [],
            ]);
            $response = new Response(400, $this->getHeaders(), $data);
        }

        return new ApiResponse($response, $this->getVersion());
    }
}
