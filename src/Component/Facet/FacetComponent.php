<?php

namespace BenTools\OpenCubes\Component\Facet;

use BenTools\OpenCubes\Component\Filter\FilterComponentInterface;

final class FacetComponent implements FacetComponentInterface
{

    /**
     * @var FacetInterface[]
     */
    private $facets = [];

    /**
     * FacetComponent constructor.
     * @param FacetInterface[] $facets
     */
    public function __construct(array $facets = [])
    {
        $facets = (function (FacetInterface ...$facets) {
            return $facets;
        })(...$facets);
        foreach ($facets as $facet) {
            $this->facets[$facet->getField()] = $facet;
        }
    }

    /**
     * @param FacetInterface[] ...$facets
     * @return FacetComponentInterface
     */
    public function withFacet(FacetInterface ...$facets): FacetComponentInterface
    {
        return new self($facets);
    }

    /**
     * @inheritDoc
     */
    public function withAddedFacet(FacetInterface ...$facets): FacetComponentInterface
    {
        $clone = clone $this;
        foreach ($facets as $facet) {
            $clone->facets[$facet->getField()] = $facet;
        }
        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withoutFacet(FacetInterface ...$facets): FacetComponentInterface
    {
        $clone = clone $this;
        foreach ($facets as $facet) {
            unset($clone->facets[$facet->getField()]);
        }
        return $clone;
    }

    /**
     * @return FacetInterface[]
     */
    public function getFacets(): array
    {
        return $this->facets;
    }

    /**
     * @param string $field
     * @return FacetInterface|null
     */
    public function getFacet(string $field): ?FacetInterface
    {
        return $this->facets[$field] ?? null;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function hasFacet(string $field): bool
    {
        return isset($this->facets[$field]);
    }

    /**
     * @param string $field
     * @param null   $value
     * @return bool
     */
    public function isFacetApplied(FilterComponentInterface $filterComponent, string $field, FacetValue $value = null): bool
    {
        if (2 === func_num_args()) {
            return $this->hasFacet($field) && $filterComponent->isFilterApplied($field);
        }
        return $this->hasFacet($field) && $filterComponent->isFilterApplied($field, $value->getValue());
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->facets);
    }
}
