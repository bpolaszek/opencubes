<?php

namespace BenTools\OpenCubes\Component\Filter;

final class CompositeFilter implements CompositeFilterInterface
{
    use NegateFilterTrait;

    /**
     * @var string
     */
    private $field;

    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * @var string
     */
    private $operator;

    /**
     * CompositeFilter constructor.
     * @param string $field
     * @param array  $filters
     * @param string $operator
     * @throws \InvalidArgumentException
     */
    public function __construct(string $field, array $filters, string $operator = self::AND)
    {
        $filters = (function (FilterInterface ...$filters) use ($field) {
            foreach ($filters as $filter) {
                if ($filter->getField() !== $field) { // Composite filters must share the same field
                    throw new \InvalidArgumentException(
                        sprintf('Expected %s filters, got %s', $field, $filter->getField())
                    );
                }
            }
            return $filters;
        })(...$filters);
        $this->field = $field;
        $this->filters = $filters;
        $this->operator = $operator;
    }

    /**
     * @inheritDoc
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @inheritDoc
     */
    public function isApplied($value): bool
    {
        foreach ($this->filters as $filter) {
            if ($filter->isApplied($value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->filters);
    }
}
