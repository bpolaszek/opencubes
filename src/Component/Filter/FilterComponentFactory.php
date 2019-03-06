<?php

namespace BenTools\OpenCubes\Component\Filter;

use BenTools\OpenCubes\Component\ComponentFactoryInterface;
use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Filter\Model\CollectionFilter;
use BenTools\OpenCubes\Component\Filter\Model\CompositeFilter;
use BenTools\OpenCubes\Component\Filter\Model\Filter;
use BenTools\OpenCubes\Component\Filter\Model\FilterValue;
use BenTools\OpenCubes\Component\Filter\Model\RangeFilter;
use BenTools\OpenCubes\Component\Filter\Model\SimpleFilter;
use BenTools\OpenCubes\Component\Filter\Model\StringMatchFilter;
use Psr\Http\Message\UriInterface;
use function BenTools\OpenCubes\contains_only_scalars;
use function BenTools\OpenCubes\is_indexed_array;

final class FilterComponentFactory implements ComponentFactoryInterface
{
    private const OPT_COLLECTION_SATISFIED_BY = 'collection_satisfied_by';
    private const OPT_COMPOSITE_SATISFIED_BY = 'composite_satisfied_by';

    /**
     * @var FilterUriManagerInterface
     */
    private $uriManager;

    /**
     * FilterComponentFactory constructor.
     * @param FilterUriManagerInterface|null $uriManager
     */
    public function __construct(FilterUriManagerInterface $uriManager = null)
    {
        $this->uriManager = $uriManager ?? new FilterUriManager();
    }

    /**
     * @inheritDoc
     */
    public function supports(string $name): bool
    {
        return FilterComponent::getName() === $name;
    }

    /**
     * @inheritDoc
     */
    public function createComponent(UriInterface $uri, array $options = []): ComponentInterface
    {
        $rawFilters = $this->uriManager->getAppliedFilters($uri);
        $filters = [];

        foreach ($rawFilters as $field => $value) {
            $filters[] = $this->createFilter($field, $value, $uri, true, []);
        }

        return new FilterComponent($filters);
    }

    /**
     * @param string       $key
     * @param              $value
     * @param UriInterface $baseUri
     * @param bool         $applied
     * @param array        $options
     * @return Filter
     * @throws \InvalidArgumentException
     */
    private function createFilter(string $key, $value, UriInterface $baseUri, bool $applied, array $options): Filter
    {
        $options[self::OPT_COLLECTION_SATISFIED_BY] = $options[self::OPT_COLLECTION_SATISFIED_BY] ?? $this->uriManager->getOption(FilterUriManager::OPT_DEFAULT_COLLECTION_SATISFIED_BY);
        $options[self::OPT_COMPOSITE_SATISFIED_BY] = $options[self::OPT_COMPOSITE_SATISFIED_BY] ?? $this->uriManager->getOption(FilterUriManager::OPT_DEFAULT_COMPOSITE_SATISFIED_BY);

        if (is_array($value)) {
            if (is_indexed_array($value) && contains_only_scalars($value)) {
                if ($this->containsRangeFilter($value)) {
                    return $this->createCompositeFilter($key, array_map(function ($value) use ($key, $baseUri, $applied, $options) {
                        return $this->createFilter($key, $value, $baseUri, $applied, $options);
                    }, $value), $baseUri, $applied, $options);
                }

                return $this->createCollectionFilter($key, $value, $baseUri, $applied, $options);
            }

            if ($this->hasNegation($value)) {
                $negated = $value['NOT'];
                unset($value['NOT']);

                if ($this->hasSatisfiedByClause($negated, $satisfiedBy)) {
                    $negated = $negated[$satisfiedBy];
                    $options = array_replace($options, [self::OPT_COLLECTION_SATISFIED_BY => $satisfiedBy]);
                }

                $filter = $this->createFilter($key, $negated, $baseUri, $applied, $options)->negate();
                if (0 === count($value)) {
                    return $filter;
                }

                return $this->createCompositeFilter($key, array_merge([$this->createFilter($key, $value, $baseUri, $applied, $options)], [$filter]), $baseUri, $applied, $options);
            }

            if ($this->hasSatisfiedByClause($value, $satisfiedBy)) {
                return $this->createFilter($key, $value[$satisfiedBy], $baseUri, $applied, array_replace($options, [self::OPT_COLLECTION_SATISFIED_BY => $satisfiedBy]));
            }

            if ($this->hasMatchOperator($value, $operator)) {
                $value = $value[$operator];
                return $this->createStringMatchFilter($key, $value, $operator, $baseUri, $applied, $options);
            }

            throw new \InvalidArgumentException("Unable to parse filters.");
        }

        if ($this->isRangeFilter($value, $matches)) {
            return $this->createRangeFilter($key, $matches[2], $matches[3], $baseUri, $applied, $options);
        }

        return $this->createSimpleFilter($key, $value, $baseUri, $applied, $options);
    }

    /**
     * @param string       $key
     * @param array        $filters
     * @param UriInterface $baseUri
     * @param bool         $applied
     * @param array        $options
     * @return Filter
     * @throws \InvalidArgumentException
     */
    private function createCompositeFilter(string $key, array $filters, UriInterface $baseUri, bool $applied, array $options): Filter
    {
        $filter = new CompositeFilter($key, $filters, $options[self::OPT_COMPOSITE_SATISFIED_BY] ?? CompositeFilter::SATISFIED_BY_ALL);
        $filter->setApplied($applied);
        $filter->setToggleUri($applied ? $this->uriManager->buildRemoveFilterUrl($baseUri, $filter) : $this->uriManager->buildApplyFilterUrl($baseUri, $filter));
        return $filter;
    }

    /**
     * @param string       $key
     * @param              $left
     * @param              $right
     * @param UriInterface $baseUri
     * @param bool         $applied
     * @param array        $options
     * @return Filter
     */
    private function createRangeFilter(string $key, $left, $right, UriInterface $baseUri, bool $applied, array $options): Filter
    {
        $left = '*' === $left ? null : $left;
        $right = '*' === $right ? null : $right;
        $filter = new RangeFilter($key, $left, $right);
        $filter->setApplied($applied);
        $filter->setToggleUri($applied ? $this->uriManager->buildRemoveFilterUrl($baseUri, $filter) : $this->uriManager->buildApplyFilterUrl($baseUri, $filter, [$left, $right]));
        return $filter;
    }

    /**
     * @param string       $key
     * @param              $value
     * @param UriInterface $baseUri
     * @param bool         $applied
     * @param array        $options
     * @return Filter
     */
    private function createSimpleFilter(string $key, $value, UriInterface $baseUri, bool $applied, array $options): Filter
    {
        if ('NULL' === $value) {
            $value = null;
        }
        $filterValue = new FilterValue($value, $value, true);
        $filter = new SimpleFilter($key, $filterValue);
        $filter->setApplied($applied);
        $filter->setToggleUri($applied ? $this->uriManager->buildRemoveFilterUrl($baseUri, $filter) : $this->uriManager->buildApplyFilterUrl($baseUri, $filter, $value));
        $filterValue->setToggleUri($this->uriManager->buildRemoveFilterUrl($baseUri, $filter, $value));
        return $filter;
    }

    /**
     * @param              $key
     * @param              $value
     * @param              $operator
     * @param UriInterface $baseUri
     * @param bool         $applied
     * @param array        $options
     * @return Filter
     * @throws \InvalidArgumentException
     */
    private function createStringMatchFilter($key, $value, $operator, UriInterface $baseUri, bool $applied, array $options): Filter
    {
        if (is_array($value)) {
            return $this->createCompositeFilter($key, array_map(function ($value) use ($key, $baseUri, $applied, $options) {
                return $this->createFilter($key, $value, $baseUri, $applied, $options);
            }, $value), $baseUri, $applied, $options);
        }
        $filterValue = new FilterValue($value, $value, $applied);
        $filter = new StringMatchFilter($key, $filterValue, $operator);
        $filter->setApplied($applied);
        $filter->setToggleUri($applied ? $this->uriManager->buildRemoveFilterUrl($baseUri, $filter) : $this->uriManager->buildApplyFilterUrl($baseUri, $filter));
        $filterValue->setToggleUri($this->uriManager->buildRemoveFilterUrl($baseUri, $filter, $value));
        return $filter;
    }

    /**
     * @param string       $key
     * @param array        $values
     * @param UriInterface $baseUri
     * @param bool         $applied
     * @param array        $options
     * @return Filter
     * @throws \InvalidArgumentException
     */
    private function createCollectionFilter(string $key, array $values, UriInterface $baseUri, bool $applied, array $options): Filter
    {
        $values = array_values($values);
        $filterValues = array_map(function ($value) {
            return new FilterValue($value, $value, true);
        }, $values);
        $filter = new CollectionFilter($key, $filterValues, $options[self::OPT_COLLECTION_SATISFIED_BY] ?? CollectionFilter::SATISFIED_BY_ANY);
        $filter->setApplied($applied);
        $filter->setToggleUri($applied ? $this->uriManager->buildRemoveFilterUrl($baseUri, $filter) : $this->uriManager->buildApplyFilterUrl($baseUri, $filter, $values));
        return $filter;
    }

    /**
     * @param $value
     * @return bool
     */
    private function hasNegation($value): bool
    {
        return is_array($value) && array_key_exists('NOT', $value);
    }

    /**
     * @param $value
     * @return bool
     */
    private function hasSatisfiedByClause($value, &$satisfiedClause = null): bool
    {
        if (is_array($value) && (array_key_exists(CollectionFilter::SATISFIED_BY_ALL, $value) || array_key_exists(CollectionFilter::SATISFIED_BY_ANY, $value))) {
            $satisfiedClause = array_key_exists(CollectionFilter::SATISFIED_BY_ALL, $value) ? CollectionFilter::SATISFIED_BY_ALL : CollectionFilter::SATISFIED_BY_ANY;

            return true;
        }
        return false;
    }

    /**
     * @param      $value
     * @param null $operator
     * @return bool
     */
    private function hasMatchOperator($value, &$operator = null): bool
    {
        if (is_array($value)) {
            foreach (array_keys($value) as $key) {
                if (in_array($key, StringMatchFilter::OPERATORS)) {
                    $operator = $key;
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param      $value
     * @param null $matches
     * @return bool
     */
    private function isRangeFilter($value, &$matches = null): bool
    {
        return preg_match('/(^\[(.*) TO (.*)\]$)/', $value, $matches);
    }

    /**
     * @param array $values
     * @return bool
     */
    private function containsRangeFilter(array $values): bool
    {
        foreach ($values as $value) {
            if ($this->isRangeFilter($value)) {
                return true;
            }
        }

        return false;
    }
}
