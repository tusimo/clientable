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
use Tusimo\ClientAble\Service;
use Tusimo\ClientAble\Repository;

/**
 * @mixin Repository
 */
trait HasService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Supported Services.
     *
     * @var Service[]
     */
    private static $services;

    public function __call($method, $parameters)
    {
        $repository = $this->getRepository();
        if (method_exists($repository, $method)) {
            $response = $repository->{$method}(...$parameters);
            if ($response instanceof Repository) {
                return $this;
            }
            return $response;
        }
        throw new \Exception('call to undefined method in class api:' . $method . json_encode($parameters));
    }

    public function getRepository(): Repository
    {
        if (! $this->repository) {
            $this->repository = new Repository();
        }
        return $this->repository;
    }

    /**
     * Register supported service.
     */
    public static function registerService(Service $service)
    {
        static::$services[$service->getName()] = $service;
    }

    public static function service(string $service)
    {
        $self = new Api();
        $self->setService($service);
        $service = static::$services[$service] ?? '';
        $self->setBaseUri($service->getEndpoint());
        return $self;
    }
}
