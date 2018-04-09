<?php

namespace BenTools\OpenCubes\Component\Filter;

interface SimpleFilterInterface extends FilterInterface
{

    /**
     * @return mixed
     */
    public function getValue();
}
