<?php

namespace BenTools\OpenCubes\Component\Sort;

final class SortComponent implements SortComponentInterface
{

    private $sorts = [];

    /**
     * SortComponent constructor.
     * @param SortInterface[] $sorts
     */
    public function __construct(array $sorts = [])
    {
        $sorts = (function (SortInterface ...$sorts) {
            return $sorts;
        })(...$sorts);
        foreach ($sorts as $sort) {
            $this->sorts[$sort->getField()] = $sort;
        }
    }

    /**
     * @inheritDoc
     */
    public function withSort(SortInterface ...$sorts): SortComponentInterface
    {
        return new self($sorts);
    }

    /**
     * @inheritDoc
     */
    public function withAddedSort(SortInterface ...$sorts): SortComponentInterface
    {
        $clone = clone $this;
        foreach ($sorts as $sort) {
            $clone->sorts[$sort->getField()] = $sort;
        }
        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withoutSort(SortInterface ...$sorts): SortComponentInterface
    {
        $clone = clone $this;
        foreach ($sorts as $sort) {
            unset($clone->sorts[$sort->getField()]);
        }
        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getSorts(): array
    {
        return $this->sorts;
    }

    /**
     * @inheritDoc
     */
    public function getSort(string $field): ?SortInterface
    {
        return $this->sorts[$field] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function hasSort(string $field): bool
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
}
