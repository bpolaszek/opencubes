<?php

namespace BenTools\OpenCubes\Component\Sort;

use BenTools\OpenCubes\Component\ComponentInterface;
use Countable;
use IteratorAggregate;

interface SortComponentInterface extends ComponentInterface, Countable, IteratorAggregate
{

    public function clear(): void;

    /**
     * @param SortInterface $sort
     */
    public function add(SortInterface $sort): void;

    /**
     * @param SortInterface $sort
     */
    public function remove(SortInterface $sort): void;

    /**
     * @return SortInterface[]
     */
    public function all(): array;

    /**
     * @param string $field
     * @return SortInterface|null
     */
    public function get(string $field): ?SortInterface;

    /**
     * @param string $field
     * @return bool
     */
    public function has(string $field): bool;

    /**
     * Return the number of sorts.
     * @return int
     */
    public function count(): int;

    /**
     * @return SortInterface[]
     */
    public function getIterator();
}
