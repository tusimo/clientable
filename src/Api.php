<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\ClientAble;

use Tusimo\Restable\Query;
use Tusimo\ClientAble\Concern\HasApi;

class Api extends Query
{
    use HasApi;
}
