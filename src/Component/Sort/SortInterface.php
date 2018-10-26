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
     * @return array
     */
    public function getAvailableDirections(): array;

    /**
     * @param int|null $direction
     * @return bool
     */
    public function isApplied(int $direction = null): bool;

    /**
     * @param bool $applied
     */
    public function setAppliedDirection(?int $direction): void;
}
