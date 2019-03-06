<?php

namespace BenTools\OpenCubes\Component\Filter\Model;

use function BenTools\OpenCubes\stringify_uri;

final class CompositeFilter extends Filter
{
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
    private $satisfiedBy;

    /**
     * CompositeFilter constructor.
     * @param string $field
     * @param array  $filters
     * @param string $satisfiedBy
     * @throws \InvalidArgumentException
     */
    public function __construct(string $field, array $filters, string $satisfiedBy = self::SATISFIED_BY_ALL)
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
        })(...array_values($filters));
        $this->field = $field;
        $this->filters = $filters;
        $this->satisfiedBy = $satisfiedBy;
    }

    /**
     * @inheritDoc
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return string
     */
    public function getSatisfiedBy(): string
    {
        return $this->satisfiedBy;
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
            'type'         => $this->getType(),
            'field'        => $this->getField(),
            'satisfied_by' => $this->getSatisfiedBy(),
            'is_applied'   => $this->isApplied(),
            'is_negated'   => $this->isNegated(),
            'filters'      => $this->getFilters(),
        ];

        if ($this->isApplied()) {
            $output['unset_link'] = stringify_uri($this->getToggleUri());
        } else {
            $output['link'] = stringify_uri($this->getToggleUri());
        }

        return $output;
    }
}
