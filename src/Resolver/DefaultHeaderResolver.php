<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\ClientAble\Resolver;

use Tusimo\ClientAble\Contract\HeaderResolverContract;

class DefaultHeaderResolver implements HeaderResolverContract
{
    public function getHeaders(): array
    {
        return [];
    }
}
