<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\ClientAble\Concern;

use Tusimo\ClientAble\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;

trait HasQueryHelper
{
    /**
     * Return Query.
     *
     * @return static
     */
    public static function query()
    {
        return new self();
    }

    /**
     * Find Resource by resource id or id array.
     *
     * @param array|int|string $id
     *
     * @return ResourceCollection|?\Tusimo\ClientAble\Resource
     */
    public function find($id)
    {
        if ($id instanceof Arrayable) {
            $id = $id->toArray();
        }
        if (is_array($id)) {
            $response = $this->getRepository()->getByIds(
                $id,
                $this->getQuerySelect()->getSelects(),
                $this->getQueryWith()->getWith()
            );
            return $response->toResourceCollection();
        }
        $response = $this->getRepository()->get(
            $id,
            $this->getQuerySelect()->getSelects(),
            $this->getQueryWith()->getWith()
        );
        return $response->toResource();
    }

    /**
     * Get first Resource match the query.
     *
     * @return ?\Tusimo\ClientAble\Resource
     */
    public function first()
    {
        $response = $this->getRepository()->getByQuery($this->limit(1));
        return $response->toResourceCollection()->first();
    }

    /**
     * Get all resource match the query.
     *
     * @return ResourceCollection
     */
    public function get()
    {
        $response = $this->getRepository()->getByQuery($this);
        return $response->toResourceCollection();
    }

    /**
     * Alias for get.
     *
     * @return ResourceCollection
     */
    public function all()
    {
        return $this->get();
    }

    /**
     * @param $column
     * @param $key
     * @return ResourceCollection
     */
    public function pluck($column, $key)
    {
        return $this->select([$column, $key])->get()->pluck($column, $key);
    }

    /**
     * Undocumented function.
     *
     * @return LengthAwarePaginator
     */
    public function paginator()
    {
        $response = $this->getRepository()->list($this);
        return $response->toLengthAwarePaginator();
    }

    /**
     * Undocumented function.
     *
     * @return CursorPaginator
     */
    public function cursorPaginator()
    {
        $response = $this->getRepository()->list($this);
        return $response->toCursorPaginator();
    }

    /**
     * Create a resource.
     *
     * @param array|Arrayable|\Tusimo\ClientAble\Resource $resource
     * @return \Tusimo\ClientAble\Resource
     */
    public function create($resource)
    {
        if ($resource instanceof Arrayable) {
            $resource = $resource->toArray();
        }
        $response = $this->getRepository()->add($resource);
        if ($response->isServiceSuccess()) {
            return $response->toResource();
        }
        throw $response->toApiException();
    }

    /**
     * Create many resources.
     *
     * @param array|Arrayable|\Tusimo\ClientAble\ResourceCollection $resources
     * @return \Tusimo\ClientAble\ResourceCollection
     */
    public function createMany($resources)
    {
        if ($resources instanceof Arrayable) {
            $resources = $resources->toArray();
        }
        $response = $this->getRepository()->batchAdd($resources);
        if ($response->isServiceSuccess()) {
            return $response->toResourceCollection();
        }
        throw $response->toApiException();
    }

    /**
     * Update a Resource.
     *
     * @param mixed $id
     * @param array|Arrayable|\Tusimo\ClientAble\Resource $resources
     */
    public function update($id, $resources)
    {
        if ($resources instanceof Arrayable) {
            $resources = $resources->toArray();
        }
        $response = $this->getRepository()->update($id, $resources);
        if ($response->isServiceSuccess()) {
            return $response->toResource();
        }
        throw $response->toApiException();
    }

    /**
     * Undocumented function.
     *
     * @param array|Arrayable|\Tusimo\ClientAble\Resource $resources
     */
    public function updateMany($resources)
    {
        if ($resources instanceof Arrayable) {
            $resources = $resources->toArray();
        }
        $response = $this->getRepository()->batchUpdate($resources);
        $response = $this->getRepository()->batchAdd($resources);
        if ($response->isServiceSuccess()) {
            return $response->toResourceCollection();
        }
        throw $response->toApiException();    }

    /**
     * Destroy resource with resource id or ids.
     *
     * @param array|int|string $ids
     */
    public function destroy($ids)
    {
        if ($ids instanceof Arrayable) {
            $ids = $ids->toArray();
        }
        if (is_array($ids)) {
            $response = $this->getRepository()->deleteByIds($ids);
        } else {
            $response = $this->getRepository()->delete($ids);
        }
        if (!$response->isServiceSuccess()) {
            throw $response->toApiException();
        }
    }

    /**
     * Return the count num for the given resource key.
     *
     * @param string $key
     *
     * @return int
     */
    public function count($key = '*')
    {
        return $this->aggregate(__FUNCTION__, $key);
    }

    /**
     * Return  the sum of the given resource key.
     *
     * @param string $key
     *
     * @return float|int
     */
    public function sum($key)
    {
        return $this->aggregate(__FUNCTION__, $key);
    }

    /**
     * Return the max of the resource key.
     *
     * @param string $key
     *
     * @return float|int|string
     */
    public function max($key)
    {
        return $this->aggregate(__FUNCTION__, $key);
    }

    /**
     * Return the min of the resource key.
     *
     * @param string $key
     */
    public function min($key)
    {
        return $this->aggregate(__FUNCTION__, $key);
    }

    /**
     * Return the avg of the resource key.
     *
     * @param string $key
     */
    public function avg($key)
    {
        return $this->aggregate(__FUNCTION__, $key);
    }

    /**
     * Return aggregate.
     *
     * @param mixed $key
     * @return mixed
     */
    private function aggregate(string $method, $key)
    {
        $keys = $key;
        if (! is_array($key)) {
            $keys = [$key];
        }
        $query = clone $this;
        $query->withAggregates($method, $keys);
        $response = $query->getRepository()->aggregate($query);
        if (is_array($key)) {
            return $response->toResource()->get($method) ?? [];
        }
        return $response->toResource()->get($method)[$key] ?? 0;
    }
}
