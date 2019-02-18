<?php

namespace BenTools\OpenCubes\Component\Filter;

use BenTools\OpenCubes\Component\Filter\Model\CollectionFilter;
use BenTools\OpenCubes\Component\Filter\Model\CompositeFilter;
use BenTools\OpenCubes\Component\Filter\Model\Filter;
use BenTools\OpenCubes\Component\Filter\Model\RangeFilter;
use BenTools\OpenCubes\Component\Filter\Model\SimpleFilter;
use BenTools\OpenCubes\Component\Filter\Model\StringMatchFilter;
use BenTools\OpenCubes\Component\Pager\PagerUriManager;
use BenTools\OpenCubes\Component\Pager\PagerUriManagerInterface;
use BenTools\OpenCubes\OptionsTrait;
use Psr\Http\Message\UriInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function BenTools\QueryString\query_string;
use function BenTools\QueryString\withoutNumericIndices;

final class FilterUriManager implements FilterUriManagerInterface
{
    use OptionsTrait;

    public const OPT_FILTER_QUERY_PARAM = 'query_param';
    public const OPT_DEFAULT_SATISFIED_BY = 'default_satisfied_by';

    /**
     * @var PagerUriManager
     */
    private $pagerUriManager;

    /**
     * SortUriManager constructor.
     * @param array $options
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function __construct(array $options = [], PagerUriManagerInterface $pagerUriManager = null)
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefaults([
            self::OPT_FILTER_QUERY_PARAM => 'filters',
            self::OPT_DEFAULT_SATISFIED_BY => CollectionFilter::ANY,
        ]);

        $this->options = $optionsResolver->resolve($options);
        $this->pagerUriManager = $pagerUriManager ?? new PagerUriManager();
    }

    /**
     * @inheritDoc
     */
    public function getAppliedFilters(UriInterface $uri): array
    {
        $qs = query_string($uri);

        return (array) $qs->getParam($this->getOption(self::OPT_FILTER_QUERY_PARAM));
    }

    /**
     * @inheritDoc
     */
    public function buildApplyFilterUrl(UriInterface $uri, Filter $filter, $value = null): UriInterface
    {
        $uri = $this->pagerUriManager->buildPageUri($uri, 1);
        $qs = query_string($uri);
        $currentFilters = $qs->getParam($this->getOption(self::OPT_FILTER_QUERY_PARAM)) ?? [];

        if ($filter instanceof CollectionFilter) {
            $values = func_num_args() > 2 ? array_merge($filter->getValues(), [$value]) : $filter->getValues();
            if ($filter->getSatisfiedBy() !== $this->getOption(self::OPT_DEFAULT_SATISFIED_BY)) {
                $values = [$filter->getSatisfiedBy() => $values];
            }
            $values = $filter->isNegated() ? ['NOT' => $values] : $values;
            $currentFilters[$filter->getField()] = $values;
        }

        if ($filter instanceof SimpleFilter) {
            $value = func_num_args() > 2 ? $value : $filter->getValue();
            $currentFilters[$filter->getField()] = $filter->isNegated() ? ['NOT' => $value] : $value;
        }

        if ($filter instanceof RangeFilter) {
            $value = func_num_args() > 2 ? (array) $value : [$filter->getLeft(), $filter->getRight()];
            $normalizedvalue = sprintf('[%s TO %s]', $value[0] ?? '*', $value[1] ?? '*');
            $currentFilters[$filter->getField()] = $filter->isNegated() ? ['NOT' => $normalizedvalue] : $normalizedvalue;
        }

        if ($filter instanceof StringMatchFilter) {
            $value = func_num_args() > 2 ? $value : $filter->getValue();
            $normalizedvalue = [$filter->getOperator() => $value];
            $currentFilters[$filter->getField()] = $filter->isNegated() ? ['NOT' => $normalizedvalue] : $normalizedvalue;
        }

        if ($filter instanceof CompositeFilter) {
            $compositeFilter = $currentFilters[$filter->getField()] ?? [];
            foreach ($filter->getFilters() as $subFilter) {
                $compositeFilter = array_merge_recursive($compositeFilter, query_string($this->buildApplyFilterUrl($uri, $subFilter))->getParam($this->getOption(self::OPT_FILTER_QUERY_PARAM), $subFilter->getField()) ?? []);
            }

            $currentFilters[$filter->getField()] = $compositeFilter;
        }

        $qs = $qs->withParam($this->getOption(self::OPT_FILTER_QUERY_PARAM), $currentFilters);

        return $uri->withQuery((string) $qs->withRenderer(withoutNumericIndices()));
    }

    /**
     * @inheritDoc
     */
    public function buildRemoveFilterUrl(UriInterface $uri, Filter $filter, $valueToRemove = null): UriInterface
    {
        $uri = $this->pagerUriManager->buildPageUri($uri, 1);
        $qs = query_string($uri);
        $currentFilters = $qs->getParam($this->getOption(self::OPT_FILTER_QUERY_PARAM)) ?? [];

        if (3 === func_num_args() && $filter instanceof CollectionFilter) {
            $filter = $filter->withoutValue($valueToRemove);

            if ([] === $filter->getValues()) {
                $qs = $qs->withoutParam($this->getOption(self::OPT_FILTER_QUERY_PARAM), $filter->getField());
            } else {
                $currentFilters[$filter->getField()] = $filter->isNegated() ? ['NOT' => $filter->getValues()] : $filter->getValues();
                $qs = $qs->withParam($this->getOption(self::OPT_FILTER_QUERY_PARAM), $currentFilters);
            }
        }
        $qs = $qs->withoutParam($this->getOption(self::OPT_FILTER_QUERY_PARAM), $filter->getField());

        return $uri->withQuery((string) $qs->withRenderer(withoutNumericIndices()));
    }
}
