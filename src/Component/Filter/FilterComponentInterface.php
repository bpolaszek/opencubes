<?php

namespace BenTools\OpenCubes\Component\Filter;

use BenTools\OpenCubes\Component\ComponentInterface;
use Countable;

interface FilterComponentInterface extends ComponentInterface, Countable
{
    /**
     * @param FilterInterface[] $filters
     * @return FilterComponentInterface
     */
    public function withFilter(FilterInterface ...$filters): FilterComponentInterface;

    /**
     * @param array $filters
     * @return FilterComponentInterface
     */
    public function withAddedFilter(FilterInterface ...$filters): FilterComponentInterface;

    /**
     * @param array $filters
     * @return FilterComponentInterface
     */
    public function withoutFilter(FilterInterface ...$filters): FilterComponentInterface;

    /**
     * @return FilterInterface[]
     */
    public function getFilters(): array;

    /**
     * @param string $field
     * @return FilterInterface|null
     */
    public function getFilter(string $field): ?FilterInterface;

    /**
     * @param string $field
     * @return bool
     */
    public function hasFilter(string $field): bool;

    /**
     * @param string $field
     * @param null   $value
     * @return bool
     */
    public function isFilterApplied(string $field, $value = null): bool;

    /**
     * Return the number of filters.
     * @return int
     */
    public function count(): int;
}
