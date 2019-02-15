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
use function BenTools\OpenCubes\is_indexed_array;
use Psr\Http\Message\UriInterface;
use function BenTools\OpenCubes\contains_only_scalars;
use function BenTools\OpenCubes\is_sequential_array;

final class FilterComponentFactory implements ComponentFactoryInterface
{
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
            $filters[] = $this->createFilter($field, $value, $uri, true);
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
    private function createFilter(string $key, $value, UriInterface $baseUri, bool $applied, array $options = []): Filter
    {
        if (is_array($value)) {

            if ($this->hasSatisfiedByClause($value, $satisfiedBy)) {
                return $this->createFilter($key, $value[$satisfiedBy], $baseUri, $applied, ['satisfied_by' => $satisfiedBy]);
            }

            if (is_indexed_array($value) && contains_only_scalars($value)) {
                $satisfiedBy = $options['satisfied_by'] ?? $this->uriManager->getOption(FilterUriManager::OPT_DEFAULT_SATISFIED_BY);
                return $this->createCollectionFilter($key, $value, $baseUri, $applied, $satisfiedBy);
            }

            if ($this->hasNegation($value)) {
                $negated = $value['NOT'];
                unset($value['NOT']);
                $filter = $this->createFilter($key, $negated, $baseUri, $applied)->negate();
                if (0 === count($value)) {
                    return $filter;
                }

                return $this->createCompositeFilter($key, array_merge([$filter], [$this->createFilter($key, $value, $baseUri, $applied)]), CompositeFilter::AND_OPERATOR, $baseUri, $applied);
            }

            if ($this->hasMatchOperator($value, $operator)) {
                return $this->createStringMatchFilter($key, $value[$operator], $operator, $baseUri, $applied);
            }

            throw new \InvalidArgumentException("Unable to parse filters.");
        }

        if (preg_match('/(^\[(.*) TO (.*)\]$)/', $value, $matches)) {
            return $this->createRangeFilter($key, $matches[2], $matches[3], $baseUri, $applied);
        }

        return $this->createSimpleFilter($key, $value, $baseUri, $applied);
    }

    /**
     * @param string       $key
     * @param array        $filters
     * @param string       $operator
     * @param UriInterface $baseUri
     * @param bool         $applied
     * @return CompositeFilter
     * @throws \InvalidArgumentException
     */
    private function createCompositeFilter(string $key, array $filters, string $operator, UriInterface $baseUri, bool $applied): CompositeFilter
    {
        $filter = new CompositeFilter($key, $filters, $operator);
        $filter->setApplied($applied);
        $filter->setToggleUri($applied ? $this->uriManager->buildRemoveFilterUrl($baseUri, $filter) : $this->uriManager->buildApplyFilterUrl($baseUri, $filter));
        return $filter;
    }

    /**
     * @param string $key
     * @param        $left
     * @param        $right
     * @return RangeFilter
     */
    private function createRangeFilter(string $key, $left, $right, UriInterface $baseUri, bool $applied): RangeFilter
    {
        $left = '*' === $left ? null : $left;
        $right = '*' === $right ? null : $right;
        $filter = new RangeFilter($key, $left, $right);
        $filter->setApplied($applied);
        $filter->setToggleUri($applied ? $this->uriManager->buildRemoveFilterUrl($baseUri, $filter) : $this->uriManager->buildApplyFilterUrl($baseUri, $filter, [$left, $right]));
        return $filter;
    }

    /**
     * @param string $key
     * @param        $value
     * @return SimpleFilter
     */
    private function createSimpleFilter(string $key, $value, UriInterface $baseUri, bool $applied): SimpleFilter
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
     * @param      $key
     * @param      $value
     * @param      $operator
     * @param bool $applied
     * @return StringMatchFilter
     */
    private function createStringMatchFilter($key, $value, $operator, UriInterface $baseUri, bool $applied): StringMatchFilter
    {
        $filterValue = new FilterValue($value, $value, $applied);
        $filter = new StringMatchFilter($key, $filterValue, $operator);
        $filter->setApplied($applied);
        $filter->setToggleUri($applied ? $this->uriManager->buildRemoveFilterUrl($baseUri, $filter) : $this->uriManager->buildApplyFilterUrl($baseUri, $filter));
        $filterValue->setToggleUri($this->uriManager->buildRemoveFilterUrl($baseUri, $filter, $value));
        return $filter;
    }

    /**
     * @param string $key
     * @param array  $values
     * @return CollectionFilter
     */
    private function createCollectionFilter(string $key, array $values, UriInterface $baseUri, bool $applied, string $satisfiedBy): CollectionFilter
    {
        $values = array_values($values);
        $filterValues = array_map(function ($value) {
            return new FilterValue($value, $value, true);
        }, $values);
        $filter = new CollectionFilter($key, $filterValues, $satisfiedBy);
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
        if (is_array($value) && (array_key_exists(CollectionFilter::ALL, $value) || array_key_exists(CollectionFilter::ANY, $value))) {
            $satisfiedClause = array_key_exists(CollectionFilter::ALL, $value) ? CollectionFilter::ALL : CollectionFilter::ANY;

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
}
