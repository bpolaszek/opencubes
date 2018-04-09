<?php

namespace BenTools\OpenCubes\Component\Drilldown;

use BenTools\OpenCubes\Component\ComponentInterface;
use Countable;

interface DrilldownComponentInterface extends ComponentInterface, Countable
{

    /**
     * @param DimensionInterface[] ...$dimensions
     * @return DrilldownComponentInterface
     */
    public function withDimension(DimensionInterface ...$dimensions): DrilldownComponentInterface;

    /**
     * @param DimensionInterface[] ...$dimensions
     * @return DrilldownComponentInterface
     */
    public function withAddedDimension(DimensionInterface ...$dimensions): DrilldownComponentInterface;

    /**
     * @param DimensionInterface[] ...$dimensions
     * @return DrilldownComponentInterface
     */
    public function withoutDimension(DimensionInterface ...$dimensions): DrilldownComponentInterface;

    /**
     * @return DimensionInterface[]
     */
    public function getDimensions(): array;

    /**
     * @param string $field
     * @return DimensionInterface|null
     */
    public function getDimension(string $field): ?DimensionInterface;

    /**
     * @param string $field
     * @return bool
     */
    public function hasDimension(string $field): bool;

    /**
     * Return the number of dimensions.
     *
     * @return int
     */
    public function count(): int;
}
