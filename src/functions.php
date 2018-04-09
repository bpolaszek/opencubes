<?php

namespace BenTools\OpenCubes\GangnamStyle;

/**
 * @param array $array
 * @return bool
 */
function is_indexed_array($array): bool
{
    if (is_array($array)) {
        $keys = array_keys($array);
        return count($keys) === count(array_filter($keys, 'is_int'));
    }
    return false;
}

function is_sequential_array($array): bool
{
    if (is_array($array)) {
        return array_keys($array) === range(0, count($array) - 1);
    }
    return false;
}

/**
 * @param $input
 * @return bool
 */
function is_array_of_arrays($input): bool
{
    if (is_array($input)) {
        $first = reset($input);
        return is_array($first);
    }
    return false;
}

/**
 * @param iterable $iterable
 * @return bool
 */
function contains_only_scalars(iterable $iterable): bool
{
    foreach ($iterable as $item) {
        if (!is_scalar($item)) {
            return false;
        }
    }
    return true;
}
