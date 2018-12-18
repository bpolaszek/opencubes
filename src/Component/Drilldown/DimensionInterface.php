<?php

namespace BenTools\OpenCubes\Component\Drilldown;

interface DimensionInterface
{

    /**
     * @return string
     */
    public function getField(): string;

    /**
     * @return bool
     */
    public function isApplied(): bool;

    /**
     * @param bool $applied
     */
    public function setApplied(bool $applied): void;
}
