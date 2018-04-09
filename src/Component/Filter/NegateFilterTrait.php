<?php

namespace BenTools\OpenCubes\Component\Filter;

trait NegateFilterTrait
{
    private $negated = false;

    /**
     * @inheritDoc
     */
    public function isNegated(): bool
    {
        return $this->negated;
    }

    /**
     * @inheritDoc
     */
    public function negate(): FilterInterface
    {
        $clone = clone $this;
        $clone->negated = !$this->negated;
        return $clone;
    }
}
