<?php

namespace BenTools\OpenCubes\GangnamStyle\Filter;

use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Filter\CollectionFilter;
use BenTools\OpenCubes\Component\Filter\CompositeFilter;
use BenTools\OpenCubes\Component\Filter\FilterComponent;
use BenTools\OpenCubes\Component\Filter\FilterComponentInterface;
use BenTools\OpenCubes\Component\Filter\FilterInterface;
use BenTools\OpenCubes\Component\Filter\RangeFilter;
use BenTools\OpenCubes\Component\Filter\SimpleFilter;
use function BenTools\OpenCubes\GangnamStyle\contains_only_scalars;
use function BenTools\OpenCubes\GangnamStyle\is_sequential_array;
use BenTools\OpenCubes\Request\RequestParserException;
use BenTools\OpenCubes\Request\RequestParserInterface;
use BenTools\QueryString\Parser\QueryStringParserInterface;
use BenTools\QueryString\Renderer\ArrayValuesNormalizerRenderer;
use BenTools\QueryString\Renderer\QueryStringRendererInterface;
use Psr\Http\Message\RequestInterface;
use function BenTools\QueryString\query_string;

final class FiltersRequestParser implements RequestParserInterface
{
    /**
     * @var string
     */
    private $qsParam;

    /**
     * @var QueryStringParserInterface|null
     */
    private $queryStringParser;

    /**
     * @var QueryStringRendererInterface|null
     */
    private $queryStringRenderer;

    /**
     * FiltersParser constructor.
     * @param string                            $queryStringParam
     * @param $
     * @param QueryStringParserInterface|null   $queryStringParser
     */
    public function __construct(
        string $queryStringParam,
        QueryStringParserInterface $queryStringParser = null
    ) {
        $this->qsParam = $queryStringParam;
        $this->queryStringParser = $queryStringParser;
        $this->queryStringRenderer = ArrayValuesNormalizerRenderer::factory();
    }

    /**
     * @inheritDoc
     */
    public function supportsComponent(ComponentInterface $component): bool
    {
        return $component instanceof FilterComponentInterface;
    }

    /**
     * @param RequestInterface              $request
     * @param FilterComponentInterface|null $component
     * @return FilterComponentInterface
     */
    public function parseRequest(RequestInterface $request, ComponentInterface $component = null): ComponentInterface
    {
        $component = $component ?? new FilterComponent();
        $qs = query_string($request->getUri(), $this->queryStringParser)->withRenderer($this->queryStringRenderer);
        $rawFilters = $qs->getParam($this->qsParam) ?? [];

        if (!is_array($rawFilters)) {
            return $component;
        }

        foreach ($rawFilters as $key => $value) {
            $component->add($this->createFilter($key, $value));
        }

        return $component;
    }

    /**
     * @param string $key
     * @param        $value
     * @return FilterInterface
     * @throws \InvalidArgumentException
     */
    private function createFilter(string $key, $value): FilterInterface
    {
        if (is_array($value)) {
            if (is_sequential_array($value) && contains_only_scalars($value)) {
                return $this->createCollectionFilter($key, $value);
            }

            if ($this->hasNegation($value)) {
                $negated = $value['NOT'];
                unset($value['NOT']);
                $filter = $this->createFilter($key, $negated)->negate();
                if (0 === count($value)) {
                    return $filter;
                }
                return new CompositeFilter($key, array_merge([$filter], [$this->createFilter($key, $value)]));
            }

            throw new RequestParserException("Unable to parse filters.");
        }

        if (preg_match('/(^\[(.*) TO (.*)\]$)/', $value, $matches)) {
            return $this->createRangeFilter($key, $matches[2], $matches[3]);
        }

        return $this->createSimpleFilter($key, $value);
    }

    /**
     * @param string $key
     * @param        $left
     * @param        $right
     * @return RangeFilter
     */
    private function createRangeFilter(string $key, $left, $right): RangeFilter
    {
        $left = '*' === $left ? null : $left;
        $right = '*' === $right ? null : $right;
        return new RangeFilter($key, $left, $right);
    }

    /**
     * @param string $key
     * @param        $value
     * @return SimpleFilter
     */
    private function createSimpleFilter(string $key, $value): SimpleFilter
    {
        if ('NULL' === $value) {
            return new SimpleFilter($key, null);
        }
        return new SimpleFilter($key, $value);
    }

    /**
     * @param string $key
     * @param array  $values
     * @return CollectionFilter
     */
    private function createCollectionFilter(string $key, array $values): CollectionFilter
    {
        return new CollectionFilter($key, $values);
    }

    /**
     * @param $value
     * @return bool
     */
    private function hasNegation($value): bool
    {
        return is_array($value) && array_key_exists('NOT', $value);
    }
}
