<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Clientable;

use RuntimeException;

class ApiException extends RuntimeException
{
    /**
     * Undocumented variable.
     *
     * @var array
     */
    protected $meta = [];

    public function __construct(array $responseData)
    {
        $this->code = $responseData['code'] ?? 400;
        $this->message = $responseData['msg'] ?? '';
        $this->meta = $responseData['meta'] ?? [];
    }

    /**
     * Get undocumented variable.
     *
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    public function isValidationError()
    {
        return $this->code == 422;
    }

    public function isServerError()
    {
        return $this->code == 500;
    }

    public function getError()
    {
        return $this->message;
    }
}
