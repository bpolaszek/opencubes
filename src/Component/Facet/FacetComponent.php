<?php

namespace BenTools\OpenCubes\Component\Facet;

use ArrayIterator;
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
        foreach ($facets as $facet) {
            $this->add($facet);
        }
    }

    public function clear(): void
    {
        $this->facets = [];
    }

    /**
     * @inheritDoc
     */
    public function add(FacetInterface $facet): void
    {
        $this->facets[$facet->getField()] = $facet;
    }

    /**
     * @inheritDoc
     */
    public function remove(FacetInterface $facet): void
    {
        unset($this->facets[$facet->getField()]);
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->facets;
    }

    /**
     * @inheritDoc
     */
    public function get(string $field): ?FacetInterface
    {
        return $this->facets[$field] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function has(string $field): bool
    {
        return isset($this->facets[$field]);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->facets);
    }


    /**
     * @param string $field
     * @param null   $value
     * @return bool
     */
    public function isApplied(FilterComponentInterface $filterComponent, string $field, FacetValue $value = null): bool
    {
        // todo
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->facets);
    }
}
