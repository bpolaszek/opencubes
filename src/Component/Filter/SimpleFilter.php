<?php

namespace BenTools\OpenCubes\Component\Filter;

final class SimpleFilter implements SimpleFilterInterface
{
    use NegateFilterTrait;

    /**
     * @var string
     */
    private $field;
    private $value;

    /**
     * SimpleFilter constructor.
     */
    public function __construct(string $field, $value)
    {
        $this->field = $field;
        $this->value = $value;
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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function isApplied($value): bool
    {
        return $this->getValue() === $value;
    }
}
