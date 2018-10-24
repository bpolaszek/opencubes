<?php

namespace BenTools\OpenCubes\Component\Filter;

use BenTools\OpenCubes\Component\ComponentInterface;
use Countable;
use IteratorAggregate;

interface FilterComponentInterface extends ComponentInterface, Countable, IteratorAggregate
{

    public function clear(): void;

    /**
     * @param FilterInterface $filter
     */
    public function add(FilterInterface $filter): void;

    /**
     * @param FilterInterface $filter
     */
    public function remove(FilterInterface $filter): void;

    /**
     * @return FilterInterface[]
     */
    public function all(): array;

    /**
     * @param string $field
     * @return FilterInterface|null
     */
    public function get(string $field): ?FilterInterface;

    /**
     * @param string $field
     * @return bool
     */
    public function has(string $field): bool;

    /**
     * Return the number of filters.
     * @return int
     */
    public function count(): int;

    /**
     * @return FilterInterface[]
     */
    public function getIterator();
}
