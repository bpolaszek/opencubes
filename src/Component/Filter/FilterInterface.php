<?php

namespace BenTools\OpenCubes\Component\Filter;

interface FilterInterface
{

    /**
     * @return string
     */
    public function getField(): string;

    /**
     * @param $value
     * @return bool
     */
    public function isApplied($value): bool;

    /**
     * @return bool
     */
    public function isNegated(): bool;

    /**
     * Toggle negation.
     * Toggling negation twice means canceling negation.
     *
     * @return FilterInterface
     */
    public function negate(): FilterInterface;
}
