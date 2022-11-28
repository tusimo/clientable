<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
use Tusimo\Clientable\Api;

function api(string $service, string $resource)
{
    return Api::service($service)->resource($resource);
}
