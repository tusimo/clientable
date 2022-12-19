<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\ClientAble\Concern\Repository;

use Tusimo\Restable\Query;
use GuzzleHttp\Psr7\Response;
use Tusimo\Restable\QuerySelect;
use Tusimo\ClientAble\ApiResponse;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Tusimo\ClientAble\Concern\HasRepository;
use GuzzleHttp\Exception\BadResponseException;

trait HasResource
{
    use HasRequest;
    use HasRepository;

    /**
     * Get Resource by id.
     *
     * @param int|string $id
     */
    public function get($id, array $select = [], array $with = []): ApiResponse
    {
        $uri = "{$this->getApiVersion()}/{$this->getResource()}/{$id}";
        $query = new Query();
        $queryString = $query->select($this->parseSelect($select))
            ->with($with)
            ->toUriQueryString($this->getVersion());

        $uri .= '?' . $queryString;
        return $this->sendRequest('GET', $uri);
    }

    /**
     * Get Resources by Ids.
     */
    public function getByIds(array $ids, array $select = [], array $with = []): ApiResponse
    {
        $this->throwExceptionIfNotSupported();

        $idsString = implode(',', $ids);
        $uri = "{$this->getApiVersion()}/{$this->getResource()}/{$idsString}/_batch";
        $query = new Query();
        $queryString = $query->select($this->parseSelect($select))
            ->with($with)
            ->toUriQueryString($this->getVersion());

        $uri .= '?' . $queryString;
        return $this->sendRequest('GET', $uri);
    }

    /**
     * Add Resource and return Resource With Id.
     */
    public function add(array $resource): ApiResponse
    {
        $uri = "{$this->getApiVersion()}/{$this->getResource()}";
        $options['json'] = $resource;
        return $this->sendRequest('POST', $uri, $options);
    }

    /**
     * Batch add resource.
     */
    public function batchAdd(array $resources): ApiResponse
    {
        $this->throwExceptionIfNotSupported();

        $uri = "{$this->getApiVersion()}/{$this->getResource()}/_batch";
        $options['json'] = $resources;
        return $this->sendRequest('POST', $uri, $options);
    }

    /**
     * Update Resource.
     *
     * @param int|string $id
     */
    public function update($id, array $resource): ApiResponse
    {
        $uri = "{$this->getApiVersion()}/{$this->getResource()}/{$id}";
        $options['json'] = $resource;
        return $this->sendRequest('PUT', $uri, $options);
    }

    /**
     * Batch Update Resource.
     */
    public function batchUpdate(array $resources): ApiResponse
    {
        $this->throwExceptionIfNotSupported();

        $uri = "{$this->getApiVersion()}/{$this->getResource()}/_batch";
        $options['json'] = $resources;
        return $this->sendRequest('PUT', $uri, $options);
    }

    /**
     * Delete resource.
     *
     * @param int|string $id
     */
    public function delete($id): ApiResponse
    {
        $uri = "{$this->getApiVersion()}/{$this->getResource()}/{$id}";
        return $this->sendRequest('DELETE', $uri);
    }

    /**
     * Batch delete Resource.
     */
    public function deleteByIds(array $ids): ApiResponse
    {
        $this->throwExceptionIfNotSupported();

        $idsString = implode(',', $ids);
        $uri = "{$this->getApiVersion()}/{$this->getResource()}/{$idsString}/_batch";
        return $this->sendRequest('DELETE', $uri);
    }

    /**
     * Get Resource Paginator.
     */
    public function list(Query $query): ApiResponse
    {
        $query = $this->parseQuery($query);
        $path = "{$this->getApiVersion()}/{$this->getResource()}";
        $queryString = $query->toUriQueryString($this->getVersion());
        $uri = $path . '?' . $queryString;
        return $this->sendRequest('GET', $uri);
    }

    /**
     * Get Resource By Query.
     */
    public function getByQuery(Query $query): ApiResponse
    {
        $query = $this->parseQuery($query);

        $path = "{$this->getApiVersion()}/{$this->getResource()}/_batch";
        $queryString = $query->toUriQueryString($this->getVersion());
        $uri = $path . '?' . $queryString;
        return $this->sendRequest('GET', $uri);
    }

    /**
     * Get Resource aggregate By Query.
     */
    public function aggregate(Query $query): ApiResponse
    {
        $query = $this->parseQuery($query);

        $path = "{$this->getApiVersion()}/{$this->getResource()}/_aggregate";
        $queryString = $query->toUriQueryString($this->getVersion());
        $uri = $path . '?' . $queryString;
        return $this->sendRequest('GET', $uri);
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
    protected function sendRequest(string $method, string $uri, array $options = []): ApiResponse
    {
        try {
            $response = $this->restRequest($method, $uri, $options);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        } catch (RequestException $e) {
            $response = $e->getResponse();
            if (is_null($response)) {
                $response = new Response(500, $this->getHeaders(), $e->getMessage());
            }
        } catch (ConnectException $e) {
            $response = new Response(500, $this->getHeaders(), $e->getMessage());
        } catch (\Exception $e) {
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
