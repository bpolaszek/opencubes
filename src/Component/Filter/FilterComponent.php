<?php

namespace BenTools\OpenCubes\Component\Filter;

final class FilterComponent implements FilterComponentInterface
{

    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * FilterComponent constructor.
     * @param FilterInterface[] ...$filters
     */
    public function __construct(FilterInterface ...$filters)
    {
        foreach ($filters as $filter) {
            $this->filters[$filter->getField()] = $filter;
        }
    }

    /**
     * @param FilterInterface[] ...$filters
     * @return FilterComponentInterface
     */
    public function withFilter(FilterInterface ...$filters): FilterComponentInterface
    {
        return new self(...$filters);
    }

    /**
     * @inheritDoc
     */
    public function withAddedFilter(FilterInterface ...$filters): FilterComponentInterface
    {
        $clone = clone $this;
        foreach ($filters as $filter) {
            $clone->filters[$filter->getField()] = $filter;
        }
        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withoutFilter(FilterInterface ...$filters): FilterComponentInterface
    {
        $clone = clone $this;
        foreach ($filters as $filter) {
            unset($clone->filters[$filter->getField()]);
        }
        return $clone;
    }

    /**
     * @return FilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param string $field
     * @return FilterInterface|null
     */
    public function getFilter(string $field): ?FilterInterface
    {
        return $this->filters[$field] ?? null;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function hasFilter(string $field): bool
    {
        return isset($this->filters[$field]);
    }

    /**
     * @param string $field
     * @param null   $value
     * @return bool
     */
    public function isFilterApplied(string $field, $value = null): bool
    {
        if (1 === func_num_args()) {
            return $this->hasFilter($field);
        }
        return $this->hasFilter($field) && $this->getFilter($field)->isApplied($value);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->filters);
    }
}
