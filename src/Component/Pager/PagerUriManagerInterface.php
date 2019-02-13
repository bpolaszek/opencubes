<?php

namespace BenTools\OpenCubes\Component\Pager;

use Psr\Http\Message\UriInterface;

interface PagerUriManagerInterface
{

    /**
     * Extract the current page size from the given Uri.
     *
     * @param UriInterface $uri
     * @return int|null
     */
    public function getCurrentPageSize(UriInterface $uri): ?int;

    /**
     * Extract the current page number from the given Uri.
     *
     * @param UriInterface $uri
     * @return int|null
     */
    public function getCurrentPageNumber(UriInterface $uri): ?int;

    /**
     * Build an Uri with the given page number.
     *
     * @param UriInterface $uri
     * @param int          $pageNumber
     * @param bool|null    $paginationEnabled
     * @return UriInterface
     * @throws \InvalidArgumentException
     */
    public function buildPageUri(UriInterface $uri, int $pageNumber): UriInterface;

    /**
     * Build an Uri with the given page size.
     * When $size is null, the Uri should no longer carry this value.
     *
     * @param UriInterface $uri
     * @param int          $size
     * @return UriInterface
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     */
    public function buildSizeUri(UriInterface $uri, ?int $size): UriInterface;
}
