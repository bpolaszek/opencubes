<?php

namespace BenTools\OpenCubes\Component\Facet;

interface FacetValueInterface
{
    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * @return mixed
     */
    public function getLabel();

    /**
     * @return int|null
     */
    public function getNumFound(): ?int;
}
