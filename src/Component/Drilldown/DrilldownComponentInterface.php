<?php

namespace BenTools\OpenCubes\Component\Drilldown;

use BenTools\OpenCubes\Component\ComponentInterface;
use Countable;
use IteratorAggregate;

interface DrilldownComponentInterface extends ComponentInterface, Countable, IteratorAggregate
{

    public function clear(): void;

    /**
     * @param DimensionInterface $dimension
     */
    public function add(DimensionInterface $dimension): void;

    /**
     * @param DimensionInterface $dimension
     */
    public function remove(DimensionInterface $dimension): void;

    /**
     * @return DimensionInterface[]
     */
    public function all(): array;

    /**
     * @param string $field
     * @return DimensionInterface|null
     */
    public function get(string $field): ?DimensionInterface;

    /**
     * @param string $field
     * @return bool
     */
    public function has(string $field): bool;

    /**
     * Return the number of dimensions.
     *
     * @return int
     */
    public function count(): int;

    /**
     * @return DimensionInterface[]
     */
    public function getIterator();
}
