<?php

namespace BenTools\OpenCubes\Component\Sort;

use BenTools\OpenCubes\Component\ComponentInterface;
use Countable;

interface SortComponentInterface extends ComponentInterface, Countable
{

    /**
     * @param SortInterface[] ...$sorts
     * @return SortComponentInterface
     */
    public function withSort(SortInterface ...$sorts): SortComponentInterface;

    /**
     * @param SortInterface[] ...$sorts
     * @return SortComponentInterface
     */
    public function withAddedSort(SortInterface ...$sorts): SortComponentInterface;

    /**
     * @param SortInterface[] ...$sorts
     * @return SortComponentInterface
     */
    public function withoutSort(SortInterface ...$sorts): SortComponentInterface;

    /**
     * @return SortInterface[]
     */
    public function getSorts(): array;

    /**
     * @param string $field
     * @return SortInterface|null
     */
    public function getSort(string $field): ?SortInterface;

    /**
     * @param string $field
     * @return bool
     */
    public function hasSort(string $field): bool;

    /**
     * Return the number of sorts.
     * @return int
     */
    public function count(): int;
}
