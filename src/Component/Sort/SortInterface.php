<?php

namespace BenTools\OpenCubes\Component\Sort;

interface SortInterface
{

    const SORT_ASC = SORT_ASC;
    const SORT_DESC = SORT_DESC;

    /**
     * @return string
     */
    public function getField(): string;

    /**
     * @return bool
     */
    public function isAsc(): bool;

    /**
     * @return bool
     */
    public function isDesc(): bool;
}
