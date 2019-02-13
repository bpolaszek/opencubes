<?php

namespace BenTools\OpenCubes\Component\Pager;

use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Pager\Model\Page;
use BenTools\OpenCubes\Component\Pager\Model\PageSize;
use Countable;
use Psr\Http\Message\UriInterface;

final class PagerComponent implements ComponentInterface, Countable
{
    /**
     * @var UriInterface
     */
    private $baseUri;

    /**
     * @var int
     */
    private $nbItems;

    /**
     * @var int|null
     */
    private $perPage;

    /**
     * @var int
     */
    private $currentPageNumber;

    /**
     * @var int
     */
    private $delta;

    /**
     * @var PageSize[]
     */
    private $pageSizes;

    /**
     * @var PagerUriManagerInterface
     */
    private $uriManager;

    /**
     * @var Page[]
     */
    private $pages;

    /**
     * PagerComponent constructor.
     * @param UriInterface                  $baseUri
     * @param int                           $totalItems
     * @param int|null                      $perPage
     * @param int                           $currentPageNumber
     * @param int|null                      $delta
     * @param array                         $pageSizes
     * @param PagerUriManagerInterface|null $uriManager
     */
    public function __construct(
        UriInterface $baseUri,
        int $totalItems = 0,
        int $perPage = null,
        int $currentPageNumber = 1,
        int $delta = null,
        array $pageSizes = [],
        PagerUriManagerInterface $uriManager = null
    ) {
        $this->baseUri = $baseUri;
        $this->nbItems = $totalItems;
        $this->perPage = $perPage;
        $this->currentPageNumber = $currentPageNumber;

        $this->pageSizes = (function (PageSize ...$pageSizes) {
            return $pageSizes;
        })(...$pageSizes);

        $this->uriManager = $uriManager ?? new PagerUriManager();
        $this->delta = $delta;
    }

    /**
     * @param int $nbItems
     */
    public function setNbItems(int $nbItems): void
    {
        $this->nbItems = $nbItems;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return null !== $this->perPage;
    }

    /**
     * @return PageSize[]
     */
    public function getPageSizes(): array
    {
        return $this->pageSizes;
    }

    /**
     * @return Page[]
     */
    public function getPages(): array
    {
        if (null === $this->pages) {
            $this->pages = iterator_to_array($this->generatePages());
        }

        return $this->pages;
    }

    /**
     * @return \Generator
     * @throws \InvalidArgumentException
     */
    private function generatePages()
    {
        $pageNumbers = [];
        $numberOfPages = count($this);

        for ($pageNumber = 1; $pageNumber <= $numberOfPages; $pageNumber++) {
            $pageNumbers[] = $pageNumber;
        }

        if (null !== $this->delta) {
            $pageNumbers = array_filter($pageNumbers, function (int $pageNumber) {
                return $pageNumber <= ($this->getCurrentPage() + $this->delta)
                    && $pageNumber >= ($this->getCurrentPage() - $this->delta);
            });
        }

        foreach ($pageNumbers as $pageNumber) {
            yield $this->createPage($pageNumber);
        }
    }

    /**
     * @param int $pageNumber
     * @return Page
     * @throws \InvalidArgumentException
     */
    private function createPage(int $pageNumber): Page
    {
        return new Page(
            $pageNumber,
            $this->getPageCount($pageNumber),
            $this->getPageOffset($pageNumber),
            $this->isFirstPage($pageNumber),
            $this->isPreviousPage($pageNumber),
            $this->isCurrentPage($pageNumber),
            $this->isNextPage($pageNumber),
            $this->isLastPage($pageNumber),
            $this->uriManager->buildPageUri($this->baseUri, $pageNumber)
        );
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'pager';
    }

    /**
     *
     * /**
     * @return int
     */
    public function count(): int
    {
        if (0 === $this->getPerPage()) {
            return 1;
        }

        return ceil($this->getNbItems() / $this->getPerPage());
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPageNumber;
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
    public function getCurrentOffset(): int
    {
        return ($this->getCurrentPage() - 1) * $this->getPerPage();
    }

    /**
     * @param int $page
     * @return int
     */
    public function getPageOffset(int $page): int
    {
        $page = $this->snap($page);

        return max(0, ($page - 1) * $this->getPerPage());
    }

    /**
     * @param int $page
     * @return int
     */
    public function getPageCount(int $page): int
    {
        $page = $this->snap($page);

        if ($this->isLastPage($page)) {
            return ($this->getPerPage() - (($page * $this->getPerPage()) - $this->getNbItems()));
        }

        return $this->getPerPage();
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage ?? $this->nbItems;
    }

    /**
     * @param int|null $page
     * @return bool
     */
    public function isFirstPage(?int $page = null): bool
    {
        return 1 === $this->snap($page);
    }

    /**
     * @param int $page
     * @return bool
     */
    public function isPreviousPage(int $page): bool
    {
        return $this->getCurrentPage() - 1 === $page;
    }

    /**
     * @param int $page
     * @return bool
     */
    public function isCurrentPage(int $page): bool
    {
        return $this->getCurrentPage() === $page;
    }

    /**
     * @param int $page
     * @return bool
     */
    public function isNextPage(int $page): bool
    {
        return $this->snap($this->getCurrentPage() + 1) === $page;
    }

    /**
     * @param int|null $page
     * @return bool
     */
    public function isLastPage(?int $page = null): bool
    {
        return $this->getLastPage() === $this->snap($page);
    }

    /**
     * @param int|null $page
     * @return int
     */
    public function getFirstPage(): int
    {
        return 1;
    }

    /**
     * @return int
     */
    public function getLastPage(): int
    {
        return count($this);
    }

    /**
     * @return int
     */
    public function getPreviousPage(?int $page = null): int
    {
        $page = $page ?? $this->getCurrentPage();
        $page = $this->snap($page);
        return $this->snap($page - 1);
    }

    /**
     * @return int
     */
    public function getNextPage(?int $page = null): int
    {
        $page = $page ?? $this->getCurrentPage();
        $page = $this->snap($page);
        return $this->snap($page + 1);
    }

    /**
     * @param int $page
     * @return int
     */
    private function snap(?int $page): int
    {
        $page = $page ?? $this->getCurrentPage();

        if ($page < 1) {
            return 1;
        }

        $lastPage = $this->getLastPage();

        if ($page > $lastPage) {
            return $lastPage;
        }

        return $page;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'enabled'    => $this->isEnabled(),
            'per_page'   => $this->getPerPage(),
            'nb_items'   => $this->getNbItems(),
            'count'      => $this->count(),
            'first'      => $this->createPage($this->getFirstPage()),
            'previous'   => !$this->isFirstPage() ? $this->createPage($this->getPreviousPage()) : null,
            'current'    => $this->createPage($this->getCurrentPage()),
            'next'       => !$this->isLastPage() ? $this->createPage($this->getNextPage()) : null,
            'last'       => $this->createPage($this->getLastPage()),
            'pages'      => $this->getPages(),
            'page_sizes' => $this->getPageSizes(),

        ];
    }
}
