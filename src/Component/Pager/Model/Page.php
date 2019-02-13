<?php

namespace BenTools\OpenCubes\Component\Pager\Model;

use Psr\Http\Message\UriInterface;
use function BenTools\OpenCubes\stringify_uri;

final class Page implements \JsonSerializable
{
    /**
     * @var int
     */
    private $pageNumber;

    /**
     * @var int
     */
    private $nbItems;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var bool
     */
    private $isFirstPage;

    /**
     * @var bool
     */
    private $isPreviousPage;

    /**
     * @var bool
     */
    private $isCurrentPage;

    /**
     * @var bool
     */
    private $isNextPage;

    /**
     * @var bool
     */
    private $isLastPage;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * Page constructor.
     */
    public function __construct(
        int $pageNumber,
        int $nbItems,
        int $offset,
        bool $isFirstPage,
        bool $isPreviousPage,
        bool $isCurrentPage,
        bool $isNextPage,
        bool $isLastPage,
        UriInterface $uri
    ) {
        $this->pageNumber = $pageNumber;
        $this->nbItems = $nbItems;
        $this->offset = $offset;
        $this->isFirstPage = $isFirstPage;
        $this->isPreviousPage = $isPreviousPage;
        $this->isCurrentPage = $isCurrentPage;
        $this->isNextPage = $isNextPage;
        $this->isLastPage = $isLastPage;
        $this->uri = $uri;
    }

    /**
     * @return int
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    /**
     * @return int
     */
    public function getNbItems(): int
    {
        return $this->nbItems;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return bool
     */
    public function isFirstPage(): bool
    {
        return $this->isFirstPage;
    }

    /**
     * @return bool
     */
    public function isPreviousPage(): bool
    {
        return $this->isPreviousPage;
    }

    /**
     * @return bool
     */
    public function isCurrentPage(): bool
    {
        return $this->isCurrentPage;
    }

    /**
     * @return bool
     */
    public function isNextPage(): bool
    {
        return $this->isNextPage;
    }

    /**
     * @return bool
     */
    public function isLastPage(): bool
    {
        return $this->isLastPage;
    }

    /**
     * @return UriInterface
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'number'      => $this->getPageNumber(),
            'nb_items'    => $this->getNbItems(),
            'offset'      => $this->getOffset(),
            'is_first'    => $this->isFirstPage(),
            'is_previous' => $this->isPreviousPage(),
            'is_current'  => $this->isCurrentPage(),
            'is_next'     => $this->isNextPage(),
            'is_last'     => $this->isLastPage(),
            'link'        => stringify_uri($this->getUri()),
        ];
    }
}
