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
 * @param iterable $iterable
 * @return bool
 */
function contains_only_integers(iterable $iterable): bool
{
    foreach ($iterable as $item) {
        if (!is_int($item)) {
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
 * @return object
 */
function cast($value)
{
    return new class($value)
    {
        private $value;

        public function __construct(?string $value)
        {
            $this->value = $value;
        }

        /**
         * @return int
         */
        public function asInt(): int
        {
            return (int) $this->value;
        }

        /**
         * @return int|null
         */
        public function asIntOrNull(): ?int
        {
            if (0 === strlen((string) $this->value)) {
                return null;
            }

            return (int) $this->value;
        }

        /**
         * @return float
         */
        public function asFloat(): float
        {
            return (float) $this->value;
        }

        /**
         * @return float|null
         */
        public function asFloatOrNull(): ?float
        {
            if (0 === strlen((string) $this->value)) {
                return null;
            }

            return (float) $this->value;
        }

        /**
         * @return string
         */
        public function asString(): string
        {
            return (string) $this->value;
        }

        /**
         * @return string|null
         */
        public function asStringOrNull(): ?string
        {
            if (0 === strlen((string) $this->value)) {
                return null;
            }

            return (string) $this->value;
        }

        /**
         * @return bool
         */
        public function asBool(): bool
        {
            return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
        }

        /**
         * @return bool|null
         */
        public function asBoolOrNull(): ?bool
        {
            if (0 === strlen((string) $this->value)) {
                return null;
            }

            return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
        }
    };
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

/**
 * @param array $array
 * @return array
 */
function remove_null_values(array $array)
{
    return array_diff($array, array_filter($array, 'is_null'));
}

/**
 * @param iterable $items
 * @return mixed
 * @throws \InvalidArgumentException
 */
function first_of(iterable $items, bool $throwErrorWhenEmpty = false)
{
    foreach ($items as $item) {
        return $item;
    }

    if (true === $throwErrorWhenEmpty) {
        throw new \InvalidArgumentException('Expected at least 1 item');
    }

    return null;
}
