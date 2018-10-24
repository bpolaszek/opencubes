<?php

namespace BenTools\OpenCubes\Component\Filter;

use ArrayIterator;

final class FilterComponent implements FilterComponentInterface
{

    private $filters = [];

    /**
     * FilterComponent constructor.
     * @param array $filters
     */
    public function __construct(array $filters = [])
    {
        foreach ($filters as $filter) {
            $this->add($filter);
        }
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        $this->filters = [];
    }

    /**
     * @inheritDoc
     */
    public function add(FilterInterface $filter): void
    {
        $this->filters[$filter->getField()] = $filter;
    }

    /**
     * @inheritDoc
     */
    public function remove(FilterInterface $filter): void
    {
        unset($this->filters[$filter->getField()]);
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->filters;
    }

    /**
     * @inheritDoc
     */
    public function get(string $field): ?FilterInterface
    {
        return $this->filters[$field] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function has(string $field): bool
    {
        return isset($this->filters[$field]);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->filters);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->filters);
    }
}
