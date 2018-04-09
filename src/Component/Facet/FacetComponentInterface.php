<?php

namespace BenTools\OpenCubes\Component\Facet;

use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Filter\FilterComponentInterface;
use Countable;

interface FacetComponentInterface extends ComponentInterface, Countable
{

    /**
     * @param FacetInterface[] $facets
     * @return FacetComponentInterface
     */
    public function withFacet(FacetInterface ...$facets): FacetComponentInterface;

    /**
     * @param array $facets
     * @return FacetComponentInterface
     */
    public function withAddedFacet(FacetInterface ...$facets): FacetComponentInterface;

    /**
     * @param array $facets
     * @return FacetComponentInterface
     */
    public function withoutFacet(FacetInterface ...$facets): FacetComponentInterface;

    /**
     * @return FacetInterface[]
     */
    public function getFacets(): array;

    /**
     * @param string $field
     * @return FacetInterface|null
     */
    public function getFacet(string $field): ?FacetInterface;

    /**
     * @param string $field
     * @return bool
     */
    public function hasFacet(string $field): bool;

    /**
     * @param FilterComponentInterface $filterComponent
     * @param string                   $field
     * @param FacetValue|null          $value
     * @return bool
     */
    public function isFacetApplied(FilterComponentInterface $filterComponent, string $field, FacetValue $value = null): bool;

    /**
     * Return the number of facets.
     * @return int
     */
    public function count(): int;
}
