<?php

namespace BenTools\OpenCubes\Component\Filter\Model;

use function BenTools\OpenCubes\stringify_uri;

final class CompositeFilter extends Filter
{
    const AND_OPERATOR = 'AND';
    const OR_OPERATOR = 'OR';

    /**
     * @var string
     */
    private $field;

    /**
     * @var Filter[]
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
    public function __construct(string $field, array $filters, string $operator = self::AND_OPERATOR)
    {
        $filters = (function (Filter ...$filters) use ($field) {
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

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'composite';
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $output = [
            'type'       => $this->getType(),
            'field'      => $this->getField(),
            'operator'   => $this->getOperator(),
            'is_applied' => $this->isApplied(),
            'is_negated' => $this->isNegated(),
            'filters'    => $this->getFilters(),
        ];

        if ($this->isApplied()) {
            $output['unset_link'] = stringify_uri($this->getToggleUri());
        } else {
            $output['link'] = stringify_uri($this->getToggleUri());
        }

        return $output;
    }
}
