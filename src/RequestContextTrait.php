<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\ClientAble;

trait RequestContextTrait
{
    /**
     * set request context in headers.
     */
    public function setRequestContext(string $key, string $value)
    {
        $this->getRepository()->withHeader($key, $value);
        return $this;
    }

    /**
     * set current user.
     *
     * @param string $userId
     * @return static
     */
    public function asUserId($userId)
    {
        return $this->setRequestContext('X-User-ID', $userId);
    }

    /**
     * set current consumer.
     *
     * @return static
     */
    public function asConsumer(string $consumer)
    {
        return $this->setRequestContext('X-Consumer-Name', $consumer);
    }

    /**
     * set current app.
     *
     * @return static
     */
    public function asApp(string $app)
    {
        return $this->setRequestContext('X-App', $app);
    }

    /**
     * set current language.
     *
     * @return static
     */
    public function asLanguage(string $language)
    {
        return $this->setRequestContext('X-Language', $language);
    }
}
