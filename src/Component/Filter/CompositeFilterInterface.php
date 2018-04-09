<?php

namespace BenTools\OpenCubes\Component\Filter;

use Countable;

interface CompositeFilterInterface extends FilterInterface, Countable
{

    const AND = 'AND';
    const OR = 'OR';

    /**
     * @return string
     */
    public function getOperator(): string;

    /**
     * All filters MUST share the same key as the current filter.
     *
     * @return array|FilterInterface[]
     */
    public function getFilters(): array;

    /**
     * @param $value
     * @return bool
     */
    public function isApplied($value): bool;

    /**
     * @return int
     */
    public function count(): int;
}
