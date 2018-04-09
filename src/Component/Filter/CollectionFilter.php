<?php

namespace BenTools\OpenCubes\Component\Filter;

final class CollectionFilter implements CollectionFilterInterface
{
    use NegateFilterTrait;

    /**
     * @var string
     */
    private $field;
    private $values;

    /**
     * CollectionFilter constructor.
     * @param string $field
     * @param        $values
     */
    public function __construct(string $field, $values)
    {
        $this->field = $field;
        $this->values = $values;
    }

    /**
     * @inheritDoc
     */
    public function getValues(): array
    {
        return $this->values;
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
    public function isApplied($value): bool
    {
        return in_array($value, $this->values, true);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->values);
    }
}
