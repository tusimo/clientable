<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Clientable;

use Illuminate\Support\Collection;

class ResourceCollection extends Collection
{
    /**
     * Get a resource by key and value.
     *
     * @param mixed $value
     * @param string $key
     */
    public function getResource($value, $key = 'id')
    {
        return $this->keyBy($key)->get($value);
    }
}
