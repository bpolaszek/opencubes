<?php

namespace BenTools\OpenCubes\Component\Filter;

use BenTools\OpenCubes\Component\Filter\Model\Filter;
use Psr\Http\Message\UriInterface;

interface FilterUriManagerInterface
{

    /**
     * Return the raw applied filters from the given Uri.
     *
     * @param UriInterface $uri
     * @return array
     */
    public function getAppliedFilters(UriInterface $uri): array;

    /**
     * Build an Uri with the given filter.
     * The implementation MUST take care of the number of arguments: when $value is not provided,
     * the implementation MUST hydrate the Uri with the filter's default values.
     * Otherwise, the implementation MUST hydrate the Uri with the provided value only.
     *
     * @param UriInterface $uri
     * @param Filter       $filter
     * @param null         $value
     * @return UriInterface
     */
    public function buildApplyFilterUrl(UriInterface $uri, Filter $filter, $value = null): UriInterface;

    /**
     * Build an Uri without the given filter / the given filter value.
     * The implementation MUST take care of the number of arguments: when $value is not provided,
     * the implementation MUST remove the whole filter from the Uri (i.e. all its values).
     * Otherwise, the implementation MUST hydrate the Uri without the provided value only.
     *
     * @param UriInterface $uri
     * @param Filter       $filter
     * @param null         $valueToRemove
     * @return UriInterface
     */
    public function buildRemoveFilterUrl(UriInterface $uri, Filter $filter, $valueToRemove = null): UriInterface;
}
