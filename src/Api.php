<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Clientable;

use Tusimo\Restable\Query;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @mixin Repository
 * @mixin Query
 */
class Api extends Query
{
    use RequestContextTrait;

    /**
     * Undocumented variable.
     *
     * @var Repository
     */
    protected $repository;

    /**
     * Supported Services.
     *
     * @var Service[]
     */
    private static $services;

    /**
     * __Call.
     *
     * @param string $method
     * @param array $parameters
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this->getRepository(), $method)) {
            $result = $this->getRepository()->{$method}(...$parameters);
            if ($result instanceof Repository) {
                return $this;
            }
            return $result;
        }
        if (method_exists($this->getClient(), $method)) {
            $result = $this->getClient()
                ->{$method}(...$parameters);
            if ($result instanceof Client) {
                return $this;
            }
            return $result;
        }
        throw new \Exception('unsupported method call:' . $method);
    }

    /**
     * Get undocumented variable.
     *
     * @return Repository
     */
    public function getRepository()
    {
        if (! $this->repository) {
            $this->repository = new Repository();
        }
        return $this->repository;
    }

    /**
     * Set undocumented variable.
     *
     * @param Repository $repository undocumented variable
     *
     * @return self
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Get a new clone instance.
     *
     * @return static
     */
    public function clone()
    {
        return clone $this;
    }

    /**
     * Return Query.
     *
     * @return static
     */
    public function getQuery()
    {
        return $this;
    }

    /**
     * Register supported service.
     */
    public static function registerService(Service $service)
    {
        static::$services[$service->getName()] = $service;
    }

    /**
     * Get Service by service name.
     */
    public static function getService(string $name)
    {
        return static::$services[$name];
    }

    public static function service(string $service)
    {
        return static::useService($service);
    }

    /**
     * Create Api using service name.
     */
    public static function useService(string $name)
    {
        $self = new self();
        $service = static::getService($name);
        $self->getRepository()->getClient()->setBaseUri($service->getEndpoint());
        return $self;
    }

    /**
     * Set current resource.
     */
    public function resource(string $name)
    {
        $this->setResourceName($name);
        return $this;
    }

    /**
     * Find Resource by resource id or id array.
     *
     * @param array|int|string $id
     *
     * @return ResourceCollection|?\Tusimo\Clientable\Resource
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
     * @return ?\Tusimo\Clientable\Resource
     */
    public function first()
    {
        $response = $this->getByQuery($this->limit(1));
        return $response->toResourceCollection()->first();
    }

    /**
     * Get all resource match the query.
     *
     * @return ResourceCollection
     */
    public function get()
    {
        $response = $this->getByQuery($this);
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
     * Undocumented function.
     *
     * @return LengthAwarePaginator
     */
    public function paginator()
    {
        $response = $this->getRepository()->list($this->getQuery());
        return $response->toLengthAwarePaginator();
    }

    /**
     * Create a resource.
     *
     * @param array|Arrayable|\Tusimo\Clientable\Resource $resource
     * @return \Tusimo\Clientable\Resource
     */
    public function create($resource)
    {
        if ($resource instanceof Arrayable) {
            $resource = $resource->toArray();
        }
        $response = $this->getRepository()->add($resource);
        return $response->toResource();
    }

    /**
     * Create many resources.
     *
     * @param array|Arrayable|\Tusimo\Clientable\ResourceCollection $resources
     * @return \Tusimo\Clientable\ResourceCollection
     */
    public function createMany($resources)
    {
        if ($resources instanceof Arrayable) {
            $resources = $resources->toArray();
        }
        $response = $this->getRepository()->batchAdd($resources);
        return $response->toResourceCollection();
    }

    /**
     * Update a Resource.
     *
     * @param mixed $id
     * @param array|Arrayable|\Tusimo\Clientable\Resource $resources
     */
    public function update($id, $resources)
    {
        if ($resources instanceof Arrayable) {
            $resources = $resources->toArray();
        }
        $response = $this->getRepository()->update($id, $resources);
        return $response->toResource();
    }

    /**
     * Undocumented function.
     *
     * @param array|Arrayable|\Tusimo\Clientable\Resource $resources
     */
    public function updateMany($resources)
    {
        if ($resources instanceof Arrayable) {
            $resources = $resources->toArray();
        }
        $response = $this->getRepository()->batchUpdate($resources);
        return $response->toResourceCollection();
    }

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
            $this->getRepository()->deleteByIds($ids);
        } else {
            $this->getRepository()->delete($ids);
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
        if (! is_array($key)) {
            $keys = [$key];
        }
        $query = $this->clone();
        $query->withAggregates($method, $keys);
        $response = $query->getRepository()->aggregate($query);
        if (is_array($key)) {
            return $response->toResource()->get($method) ?? [];
        }
        return $response->toResource()->get($method)[$key] ?? 0;
    }
}
