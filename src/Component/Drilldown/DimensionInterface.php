<?php

namespace BenTools\OpenCubes\Component\Drilldown;

interface DimensionInterface
{

    /**
     * @return string
     */
    public function getField(): string;
}
