<?php

namespace BenTools\OpenCubes\Component\Facet;

use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Filter\FilterComponentInterface;
use Countable;
use IteratorAggregate;

interface FacetComponentInterface extends ComponentInterface, Countable, IteratorAggregate
{

    public function clear(): void;

    /**
     * @param FacetInterface $facet
     */
    public function add(FacetInterface $facet): void;

    /**
     * @param FacetInterface $facet
     */
    public function remove(FacetInterface $facet) :void;

    /**
     * @return FacetInterface[]
     */
    public function all(): array;

    /**
     * @param string $field
     * @return FacetInterface|null
     */
    public function get(string $field): ?FacetInterface;

    /**
     * @param string $field
     * @return bool
     */
    public function has(string $field): bool;

    /**
     * @param FilterComponentInterface $filterComponent
     * @param string                   $field
     * @param FacetValue|null          $value
     * @return bool
     */
    public function isApplied(FilterComponentInterface $filterComponent, string $field, FacetValue $value = null): bool;

    /**
     * Return the number of facets.
     * @return int
     */
    public function count(): int;


    /**
     * @return FacetInterface[]
     */
    public function getIterator();
}
