<?php

namespace BenTools\OpenCubes\Component\Filter;

final class RangeFilter implements RangeFilterInterface
{
    use NegateFilterTrait;

    /**
     * @var string
     */
    private $field;
    private $left;
    private $right;

    /**
     * RangeFilter constructor.
     * @param string $field
     * @param        $left
     * @param        $right
     */
    public function __construct(string $field, $left, $right)
    {
        $this->field = $field;
        $this->left = $left;
        $this->right = $right;
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
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @inheritDoc
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @inheritDoc
     */
    public function isInRange($value): bool
    {
        return $value >= $this->left
            && $value <= $this->right;
    }

    /**
     * @inheritDoc
     */
    public function isApplied($value): bool
    {
        return $this->isInRange($value);
    }
}
