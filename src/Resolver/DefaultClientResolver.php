<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\ClientAble\Resolver;

use Tusimo\ClientAble\Contract\ClientResolverContract;

class DefaultClientResolver implements ClientResolverContract
{
    public function getClient(): \GuzzleHttp\Client
    {
        return new \GuzzleHttp\Client();
    }
}
