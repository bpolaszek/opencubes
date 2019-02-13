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
     * @var array
     */
    private $values;

    /**
     * CollectionFilter constructor.
     * @param string $field
     * @param        $values
     */
    public function __construct(string $field, array $values = [])
    {
        $this->field = $field;
        $this->values = array_values($values);
    }

    /**
     * @inheritDoc
     */
    public function getValues(): array
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
        foreach ($clone->values as $v => $_value) {
            if ($_value === $value) {
                unset($clone->values[$v]);
            }
        }

        $clone->values = array_values($clone->values);

        return $clone;
    }

    /**
     * @param array $values
     * @return CollectionFilter
     */
    public function withValues(array $values): self
    {
        $clone = clone $this;
        $clone->values = array_values($values);

        return $clone;
    }

    /**
     * @param $value
     * @return bool
     */
    public function contains($value): bool
    {
        if (null === $value) {
            return in_array(null, $this->values, true);
        }

        return in_array((string) $value, $this->values, true);
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
            'type'       => $this->getType(),
            'field'      => $this->getField(),
            'is_applied' => $this->isApplied(),
            'is_negated' => $this->isNegated(),
            'values'     => $this->getValues(),
        ];

        if ($this->isApplied()) {
            $output['unset_link'] = stringify_uri($this->getToggleUri());
        } else {
            $output['link'] = stringify_uri($this->getToggleUri());
        }

        return $output;
    }
}
