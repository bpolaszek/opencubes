<?php

namespace BenTools\OpenCubes\Component\Sort;

use BenTools\OpenCubes\Component\Sort\Model\Sort;
use Psr\Http\Message\UriInterface;

interface SortUriManagerInterface
{

    /**
     * Return the applied sorts from the given Uri, as an associative array (i.e. ['some_field' => 'desc']).
     *
     * @param UriInterface $uri
     * @return array
     */
    public function getAppliedSorts(UriInterface $uri): array;

    /**
     * Build an Uri with the given sorts.
     *
     * @param UriInterface          $uri
     * @param array|string[]|Sort[] $sorts
     * @return UriInterface
     */
    public function buildSortUri(UriInterface $uri, array $sorts): UriInterface;
}
