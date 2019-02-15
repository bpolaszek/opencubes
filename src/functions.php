<?php
declare(strict_types=1);

namespace BenTools\OpenCubes;

use BenTools\UriFactory\UriFactory;
use BenTools\UriFactory\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use function BenTools\QueryString\query_string;
use function BenTools\QueryString\withoutNumericIndices;
use function BenTools\UriFactory\Helper\uri;

function is_sequential_array($array): bool
{
    if (!is_array($array)) {
        return false;
    }

    return array_keys($array) === range(0, count($array) - 1);
}

function is_indexed_array($array): bool
{
    if (!is_array($array)) {
        return false;
    }

    try {
        (function (int...$values) {
            return $values;
        })(...array_keys($array));
        return true;
    } catch (\TypeError $e) {
        return false;
    }
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

/**
 * @param UriFactoryInterface|null $factory
 * @return UriInterface
 * @throws \RuntimeException
 */
function current_location(UriFactoryInterface $factory = null): UriInterface
{
    if ('cli' === php_sapi_name()) {
        return uri('/');
    }

    return UriFactory::factory()->createUriFromCurrentLocation($factory);
}

/**
 * @param $value
 * @return StringCaster
 */
function cast($value): StringCaster
{
    return new StringCaster($value);
}

/**
 * @param UriInterface|null $uri
 * @return string|null
 */
function stringify_uri(?UriInterface $uri): ?string
{
    if (null === $uri) {
        return null;
    }

    $uri = $uri->withQuery(
        (string) query_string($uri)->withRenderer(withoutNumericIndices())
    );

    return rawurldecode((string) $uri);
}

function remove_null_values(array $array)
{
    return array_diff($array, array_filter($array, 'is_null'));
}
