<?php

namespace BenTools\OpenCubes\Component\Filter;

use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Filter\Model\Filter;
use Countable;
use IteratorAggregate;
use JsonSerializable;

final class FilterComponent implements ComponentInterface, IteratorAggregate, Countable, JsonSerializable
{

    /**
     * @var Filter[]
     */
    private $filters = [];

    /**
     * FilterComponent constructor.
     * @param Filter[] $filters
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
    public static function getName(): string
    {
        return 'filter';
    }

    /**
     * @param Filter $filter
     */
    public function add(Filter $filter): void
    {
        $this->filters[$filter->getField()] = $filter;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function has(string $field): bool
    {
        foreach ($this->filters as $filter) {
            if ($field === $filter->getField()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $field
     * @return Filter
     * @throws \InvalidArgumentException
     */
    public function get(string $field): Filter
    {
        foreach ($this->filters as $filter) {
            if ($field === $filter->getField()) {
                return $filter;
            }
        }

        throw new \InvalidArgumentException(sprintf('Unknown filter %s', $field));
    }

    /**
     * @return Filter[]
     */
    public function all(): array
    {
        return $this->filters;
    }

    /**
     * @return Filter[]
     */
    public function getAppliedFilters(): array
    {
        return array_values(
            array_filter(
                $this->filters,
                function (Filter $filter) {
                    return $filter->isApplied();
                }
            )
        );
    }

    /**
     * @return Filter[]
     */
    public function getIterator(): iterable
    {
        return new \ArrayIterator($this->filters);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->filters);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return $this->all();
    }
}
