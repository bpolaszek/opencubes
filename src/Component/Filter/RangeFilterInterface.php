<?php

namespace BenTools\OpenCubes\Component\Filter;

interface RangeFilterInterface extends FilterInterface
{

    /**
     * @return mixed
     */
    public function getLeft();

    /**
     * @return mixed
     */
    public function getRight();

    /**
     * @param $value
     * @return bool
     */
    public function isInRange($value): bool;
}
