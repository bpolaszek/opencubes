<?php

namespace BenTools\OpenCubes\GangnamStyle\Filter;

use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Filter\CompositeFilterInterface;
use BenTools\OpenCubes\Component\Filter\FilterComponentInterface;
use BenTools\OpenCubes\Component\Filter\FilterInterface;
use BenTools\OpenCubes\Component\Filter\RangeFilterInterface;
use BenTools\OpenCubes\Component\Filter\SimpleFilterInterface;
use BenTools\OpenCubes\Request\RequestBuilderInterface;
use BenTools\QueryString\Parser\NativeParser;
use BenTools\QueryString\Parser\QueryStringParserInterface;
use BenTools\QueryString\Renderer\ArrayValuesNormalizerRenderer;
use BenTools\QueryString\Renderer\QueryStringRendererInterface;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Psr\Http\Message\RequestInterface;
use function BenTools\QueryString\query_string;
use function BenTools\UriFactory\Helper\current_location;

final class FiltersRequestBuilder implements RequestBuilderInterface
{
    /**
     * @var string
     */
    private $qsParam;

    /**
     * @var MessageFactory|null
     */
    private $requestFactory;

    /**
     * @var QueryStringParserInterface
     */
    private $queryStringParser;
    
    /**
     * @var QueryStringRendererInterface
     */
    private $queryStringRenderer;

    /**
     * FiltersRequestBuilder constructor.
     * @param string                            $queryStringParam
     * @param MessageFactory|null               $requestFactory
     * @param QueryStringParserInterface|null   $queryStringParser
     * @param QueryStringRendererInterface|null $queryStringRenderer
     * @throws \Http\Discovery\Exception\NotFoundException
     */
    public function __construct(
        string $queryStringParam,
        MessageFactory $requestFactory = null,
        QueryStringParserInterface $queryStringParser = null,
        QueryStringRendererInterface $queryStringRenderer = null
    ) {
        $this->qsParam = $queryStringParam;
        $this->requestFactory = $requestFactory ?? MessageFactoryDiscovery::find();
        $this->queryStringParser = $queryStringParser ?? new NativeParser();
        $this->queryStringRenderer = $queryStringRenderer ?? ArrayValuesNormalizerRenderer::factory();
    }

    /**
     * @inheritDoc
     */
    public function supportsComponent(ComponentInterface $component): bool
    {
        return $component instanceof FilterComponentInterface;
    }

    /**
     * @param FilterComponentInterface $component
     * @param RequestInterface|null    $request
     * @return RequestInterface
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function buildRequest(ComponentInterface $component, RequestInterface $request = null): RequestInterface
    {
        $request = $request ?? $this->requestFactory->createRequest('GET', current_location());
        $qs = query_string($request->getUri(), $this->queryStringParser)->withRenderer($this->queryStringRenderer);
        $filters = $qs->getParam($this->qsParam) ?? [];

        if (0 === count($component->getFilters()) && 0 === count($filters)) {
            return $request->withUri(
                $request->getUri()->withQuery(
                    (string) $qs->withoutParam($this->qsParam)
                )
            );
        }

        foreach ($component->getFilters() as $filter) {
            $this->hydrateFilter($filter, $filters);
        }

        return $request->withUri(
            $request->getUri()->withQuery(
                (string) $qs->withParam($this->qsParam, $filters)
            )
        );
    }

    /**
     * @param FilterInterface $filter
     * @param array           $filters
     */
    private function hydrateFilter(FilterInterface $filter, array &$filters): void
    {
        $filters[$filter->getField()] = $this->getFilterValue($filter);
    }

    /**
     * @param FilterInterface $filter
     * @return array|mixed|string
     */
    private function getFilterValue(FilterInterface $filter)
    {
        if ($filter instanceof CompositeFilterInterface) {
            return $this->getCompositeFilterValueAsArray($filter);
        }

        if ($filter instanceof SimpleFilterInterface) {
            return $this->getSimpleFilterValueAsString($filter);
        }

        if ($filter instanceof RangeFilterInterface) {
            return $this->getRangeFilterValueAsString($filter);
        }
    }

    /**
     * @param SimpleFilterInterface $filter
     * @return mixed|string
     */
    private function getSimpleFilterValueAsString(SimpleFilterInterface $filter)
    {
        return $filter->getValue() ?? 'NULL';
    }

    /**
     * @param RangeFilterInterface $filter
     * @return string
     */
    private function getRangeFilterValueAsString(RangeFilterInterface $filter)
    {
        return sprintf('[%s TO %s]', $filter->getLeft() ?? '*', $filter->getRight() ?? '*');
    }

    /**
     * @param CompositeFilterInterface $compositeFilter
     * @return array
     */
    private function getCompositeFilterValueAsArray(CompositeFilterInterface $compositeFilter)
    {
        $values = [];
        foreach ($compositeFilter->getFilters() as $filter) {
            $values[] = $this->getFilterValue($filter);
        }
        return $values;
    }
}
