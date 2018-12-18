<?php

namespace BenTools\OpenCubes\GangnamStyle\Drilldown;

use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Drilldown\Dimension;
use BenTools\OpenCubes\Component\Drilldown\DimensionInterface;
use BenTools\OpenCubes\Component\Drilldown\DrilldownComponent;
use BenTools\OpenCubes\Component\Drilldown\DrilldownComponentInterface;
use BenTools\OpenCubes\Request\RequestParserInterface;
use BenTools\QueryString\Parser\QueryStringParserInterface;
use function BenTools\QueryString\query_string;
use BenTools\QueryString\Renderer\ArrayValuesNormalizerRenderer;
use BenTools\QueryString\Renderer\QueryStringRendererInterface;
use Psr\Http\Message\RequestInterface;

final class DrilldownRequestParser implements RequestParserInterface
{
    /**
     * @var string
     */
    private $qsParam;

    /**
     * @var QueryStringParserInterface
     */
    private $queryStringParser;

    /**
     * @var QueryStringRendererInterface
     */
    private $queryStringRenderer;

    /**
     * DrilldownRequestParser constructor.
     * @param string                            $queryStringParam
     * @param QueryStringParserInterface|null   $queryStringParser
     * @param QueryStringRendererInterface|null $queryStringRenderer
     */
    public function __construct(
        string $queryStringParam,
        QueryStringParserInterface $queryStringParser = null,
        QueryStringRendererInterface $queryStringRenderer = null
    ) {
        $this->qsParam = $queryStringParam;
        $this->queryStringParser = $queryStringParser;
        $this->queryStringRenderer = $queryStringRenderer ?? ArrayValuesNormalizerRenderer::factory();
    }

    /**
     * @param RequestInterface        $request
     * @param DrilldownComponentInterface|null $component
     * @return DrilldownComponentInterface
     */
    public function parseRequest(RequestInterface $request, ComponentInterface $component = null): ComponentInterface
    {
        $component = $component ?? new DrilldownComponent();
        $qs = query_string($request->getUri(), $this->queryStringParser)->withRenderer($this->queryStringRenderer);
        $dimensions = $qs->getParam($this->qsParam) ?? [];

        if (!is_array($dimensions)) {
            $dimensions = (array) $dimensions;
        }

        $dimensions = array_diff($dimensions, array_filter($dimensions, 'is_null'));

        foreach ($dimensions as $field) {
            if ($component->has($field)) {
                $component->get($field)->setApplied(true);
            } else {
                $component->add(new Dimension($field, true));
            }
        }

        return $component;
    }

    /**
     * @inheritDoc
     */
    public function supportsComponent(ComponentInterface $component): bool
    {
        return $component instanceof DrilldownComponentInterface;
    }
}
