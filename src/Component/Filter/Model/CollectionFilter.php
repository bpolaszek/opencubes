<?php

namespace BenTools\OpenCubes\Component\Filter\Model;

use function BenTools\OpenCubes\stringify_uri;

final class CollectionFilter extends Filter implements \Countable
{

    /**
     * @var string
     */
    private $field;

    /**
     * @var FilterValue[]
     */
    private $values;

    /**
     * @var string
     */
    private $satisfiedBy;

    /**
     * CollectionFilter constructor.
     * @param string        $field
     * @param Filtervalue[] $values
     * @param string        $satisfiedBy
     * @throws \InvalidArgumentException
     */
    public function __construct(string $field, array $values = [], string $satisfiedBy = self::SATISFIED_BY_ANY)
    {
        if (!in_array($satisfiedBy, [self::SATISFIED_BY_ANY, self::SATISFIED_BY_ALL])) {
            throw new \InvalidArgumentException(sprintf('Invalid "satisfiedBy" condition for %s', $field));
        }
        $this->field = $field;
        $this->values = (function (FilterValue ... $filterValues) {
            return $filterValues;
        })(...$values);
        $this->satisfiedBy = $satisfiedBy;
    }

    /**
     * @param string $field
     * @param array  $values
     * @param string $satisfiedBy
     * @return CollectionFilter
     * @throws \InvalidArgumentException
     */
    public static function createFromValues(string $field, array $values, string $satisfiedBy = self::SATISFIED_BY_ANY)
    {
        return new self(
            $field,
            array_map(function ($value): FilterValue {
                return new FilterValue($value);
            }, $values),
            $satisfiedBy
        );
    }

    /**
     * @inheritDoc
     */
    public function getValues(): array
    {
        return array_map(function (FilterValue $value) {
            return $value->getValue();
        }, $this->values);
    }

    /**
     * @return FilterValue[]
     */
    public function getFilterValues()
    {
        return $this->values;
    }

    /***
     * @param $value
     * @return CollectionFilter
     */
    public function withoutValue($value): self
    {
        $clone = clone $this;
        foreach ($clone->values as $v => $filterValue) {
            if ($filterValue->getValue() === $value) {
                unset($clone->values[$v]);
            }
        }

        $clone->values = array_values($clone->values);

        return $clone;
    }

    /**
     * @param $value
     * @return bool
     */
    public function contains($value): bool
    {
        if (null === $value) {
            return in_array(null, $this->getValues(), true);
        }

        return in_array((string) $value, $this->getValues(), true);
    }

    /**
     * @inheritDoc
     */
    public function getField(): string
    {
        return $this->field;
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
    public function count()
    {
        return count($this->values);
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'collection';
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
            'values'       => $this->getFilterValues(),
        ];

        if ($this->isApplied()) {
            $output['unset_link'] = stringify_uri($this->getToggleUri());
        } else {
            $output['link'] = stringify_uri($this->getToggleUri());
        }

        return $output;
    }
}
