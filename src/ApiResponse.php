<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\ClientAble;

use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    /**
     * original response.
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * the cache content.
     *
     * @var string
     */
    private $originalContents = '';

    /**
     * the decoded data.
     *
     * @var array
     */
    private $contents = [];

    /**
     * api version.
     *
     * @var string
     */
    private $version = 'v2';

    public function __construct(ResponseInterface $response, string $version = 'v2')
    {
        $this->version = $version;
        $this->response = $response;
        $this->originalContents = $this->response->getBody()->getContents();
        $this->initContents();
    }

    /**
     * 获取服务数据.
     * @return array
     */
    public function getData()
    {
        return $this->contents['data'] ?? [];
    }

    /**
     * 获取返回内容.
     * @return array
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * get the meta.
     */
    public function getMeta(): array
    {
        return $this->getContents()['meta'] ?? [];
    }

    /**
     * 返回原始数据.
     */
    public function getOriginalContents(): string
    {
        return $this->originalContents ?? '';
    }

    /**
     * 获取消息.
     * @return string
     */
    public function getMessage()
    {
        return $this->contents['msg'] ?? ($this->contents['message'] ?? '');
    }

    /**
     * 服务是否成功
     */
    public function isServiceSuccess(): bool
    {
        return $this->getServiceStatus() >= 200 && $this->getServiceStatus() < 300;
    }

    /**
     * 获取服务状态
     * @return int
     */
    public function getServiceStatus()
    {
        return intval($this->isStatusSuccess() ? ($this->contents['code'] ?? 200) : $this->response->getStatusCode());
    }

    /**
     * 请求是否成功
     */
    public function isStatusSuccess(): bool
    {
        return $this->response->getStatusCode() >= 200 && $this->response->getStatusCode() < 300;
    }

    public function returnSuccess()
    {
        return 'SUCCESS';
    }

    /**
     * Undocumented function.
     *
     * @return null|\Tusimo\ClientAble\Resource
     */
    public function toResource()
    {
        if ($this->isServiceSuccess()) {
            return $this->makeResource($this->getData());
        }
        return null;
    }

    /**
     * Response to ResourceCollection.
     *
     * @return ResourceCollection
     */
    public function toResourceCollection()
    {
        return $this->makeResourceCollection($this->getData());
    }

    /**
     * Undocumented function.
     *
     * @return LengthAwarePaginator
     */
    public function toLengthAwarePaginator()
    {
        if ($this->getVersion() === 'v1') {
            $data = $this->getData()['data'] ?? [];
            $paginator = Arr::except($this->getData(), 'data');
            $paginator['current_page'] = $paginator['current_page'] ?? $paginator['page'];
        } else {
            $data = $this->getData();
            $paginator = $this->getMeta()['paginator'] ?? [];
        }
        $data = $this->makeResourceCollection($data);
        return new LengthAwarePaginator(
            $data,
            $paginator['total'] ?? 0,
            $paginator['per_page'] ?? 10,
            $paginator['current_page'] ?? 1,
            [
                'path' => $paginator['path'] ?? '',
            ]
        );
    }

    /**
     * Response to pagination.
     * @deprecated only for v1 use purpose
     * @return ResourcePagination
     */
    public function toPagination()
    {
        return new ResourcePagination($this->getData());
    }

    /**
     * Undocumented function.
     */
    public function toApiException(): ApiException
    {
        return new ApiException($this->getContents());
    }

    /**
     * Get Response Error.
     */
    public function getError()
    {
        return $this->isServiceSuccess() ? '' : $this->getMessage();
    }

    /**
     * Get the value of version.
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Undocumented function.
     *
     * @return ResourceCollection
     */
    protected function makeResourceCollection(array $data)
    {
        $resources = [];
        foreach ($data as $item) {
            $resources[] = $this->makeResource($item);
        }
        return new ResourceCollection($resources);
    }

    /**
     * Undocumented function.
     *
     * @return \Tusimo\ClientAble\Resource
     */
    protected function makeResource(array $data)
    {
        return new Resource($data);
    }

    private function initContents()
    {
        $contents = [];
        if ($this->getOriginalContents()) {
            try {
                $contents = json_decode($this->originalContents, true);
            } catch (\Exception $e) {
                $contents = [];
            }
        }
        $this->contents = $contents;
    }
}
