<?php

namespace BenTools\OpenCubes\Component\Filter;

interface CollectionFilterInterface extends FilterInterface, \Countable
{

    /**
     * @return array
     */
    public function getValues(): array;
}
