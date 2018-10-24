<?php

namespace BenTools\OpenCubes\Component\Sort;

use ArrayIterator;

final class SortComponent implements SortComponentInterface
{

    private $sorts = [];

    /**
     * SortComponent constructor.
     * @param SortInterface[] $sorts
     */
    public function __construct(array $sorts = [])
    {
        foreach ($sorts as $sort) {
            $this->add($sort);
        }
    }

    public function clear(): void
    {
        $this->sorts = [];
    }

    /**
     * @inheritDoc
     */
    public function add(SortInterface $sort): void
    {
        $this->sorts[$sort->getField()] = $sort;
    }

    /**
     * @inheritDoc
     */
    public function remove(SortInterface $sort): void
    {
        unset($this->sorts[$sort->getField()]);
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->sorts;
    }

    /**
     * @inheritDoc
     */
    public function get(string $field): ?SortInterface
    {
        return $this->sorts[$field] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function has(string $field): bool
    {
        return isset($this->sorts[$field]);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->sorts);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->sorts);
    }
}
