<?php

declare(strict_types=1);
/**
 *
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\ClientAble;

/**
 * @deprecated only for v1 use purpose
 */
class ResourcePagination
{
    /**
     * CurrentPage num.
     *
     * @var int
     */
    protected $currentPage;

    /**
     * FirstPageUrl.
     *
     * @var string
     */
    protected $firstPageUrl;

    /**
     * FromId.
     *
     * @var int
     */
    protected $from;

    /**
     * ToId.
     *
     * @var int
     */
    protected $to;

    /**
     * NextPageUrl.
     *
     * @var string
     */
    protected $nextPageUrl;

    /**
     * ResourcePath.
     *
     * @var string
     */
    protected $path;

    /**
     * The Num Of Resource PerPage.
     *
     * @var int
     */
    protected $perPage;

    /**
     * PrevPageUrl.
     *
     * @var string
     */
    protected $prevPageUrl;

    /**
     * Total Resource Number.
     *
     * @var int
     */
    protected $total;

    /**
     * Last Page Number.
     *
     * @var int
     */
    protected $lastPage;

    /**
     * Last Page Url.
     *
     * @var string
     */
    protected $lastPageUrl;

    /**
     * ResourceCollection.
     *
     * @var ResourceCollection
     */
    protected $resourceCollection;

    public function __construct(array $data)
    {
        if (isset($data['current_page'])) {
            $this->currentPage = $data['current_page'];
        }
        if (isset($data['first_page_url'])) {
            $this->firstPageUrl = $data['first_page_url'];
        }
        if (isset($data['from'])) {
            $this->from = $data['from'];
        }
        if (isset($data['next_page_url'])) {
            $this->nextPageUrl = $data['next_page_url'];
        }
        if (isset($data['path'])) {
            $this->path = $data['path'];
        }
        if (isset($data['per_page'])) {
            $this->perPage = $data['per_page'];
        }
        if (isset($data['prev_page_url'])) {
            $this->prevPageUrl = $data['prev_page_url'];
        }
        if (isset($data['to'])) {
            $this->to = $data['to'];
        }
        if (isset($data['total'])) {
            $this->total = $data['total'];
        }
        if (isset($data['last_page_url'])) {
            $this->lastPageUrl = $data['last_page_url'];
        }
        if (isset($data['last_page'])) {
            $this->lastPage = $data['last_page'];
        }
        $resources = [];
        if (isset($data['data'])) {
            foreach ($data['data'] as $item) {
                $resource = new Resource($item);

                $resources[] = $resource;
            }
        }
        $this->resourceCollection = new ResourceCollection($resources);
    }

    /**
     * Get currentPage num.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Get firstPageUrl.
     *
     * @return string
     */
    public function getFirstPageUrl()
    {
        return $this->firstPageUrl;
    }

    /**
     * Get fromId.
     *
     * @return int
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Get toId.
     *
     * @return int
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Get nextPageUrl.
     *
     * @return string
     */
    public function getNextPageUrl()
    {
        return $this->nextPageUrl;
    }

    /**
     * Get resourcePath.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the Num Of Resource PerPage.
     *
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Get prevPageUrl.
     *
     * @return string
     */
    public function getPrevPageUrl()
    {
        return $this->prevPageUrl;
    }

    /**
     * Get resourceCollection.
     *
     * @return ResourceCollection
     */
    public function getResourceCollection()
    {
        return $this->resourceCollection;
    }

    /**
     * Get total Resource Number.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Get last Page Number.
     *
     * @return int
     */
    public function getLastPage()
    {
        return $this->lastPage;
    }

    /**
     * Get last Page Url.
     *
     * @return string
     */
    public function getLastPageUrl()
    {
        return $this->lastPageUrl;
    }
}
