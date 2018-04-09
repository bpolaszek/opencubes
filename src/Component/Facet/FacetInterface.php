<?php

namespace BenTools\OpenCubes\Component\Facet;

interface FacetInterface
{

    /**
     * @return string
     */
    public function getField(): string;

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @return FacetValueInterface[]
     */
    public function getAvailableValues(): iterable;

    /**
     * @return bool
     */
    public function allowsMultipleValues(): bool;
}
