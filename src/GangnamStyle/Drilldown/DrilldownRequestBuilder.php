<?php

namespace BenTools\OpenCubes\GangnamStyle\Drilldown;

use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Drilldown\DrilldownComponentInterface;
use BenTools\OpenCubes\Request\RequestBuilderInterface;
use BenTools\QueryString\Parser\QueryStringParserInterface;
use BenTools\QueryString\Renderer\ArrayValuesNormalizerRenderer;
use BenTools\QueryString\Renderer\QueryStringRendererInterface;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Psr\Http\Message\RequestInterface;
use function BenTools\QueryString\query_string;
use function BenTools\UriFactory\Helper\current_location;

final class DrilldownRequestBuilder implements RequestBuilderInterface
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
     * @var QueryStringParserInterface|null
     */
    private $queryStringParser;

    /**
     * @var QueryStringRendererInterface|null
     */
    private $queryStringRenderer;

    /**
     * DrilldownRequestBuilder constructor.
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
        $this->queryStringParser = $queryStringParser;
        $this->queryStringRenderer = $queryStringRenderer ?? ArrayValuesNormalizerRenderer::factory();
    }

    /**
     * @param DrilldownComponentInterface    $component
     * @param RequestInterface|null $request
     * @return RequestInterface
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function buildRequest(ComponentInterface $component, RequestInterface $request = null): RequestInterface
    {
        $request = $request ?? $this->requestFactory->createRequest('GET', current_location());
        $qs = query_string($request->getUri(), $this->queryStringParser)->withRenderer($this->queryStringRenderer);
        $dimensions = $qs->getParam($this->qsParam) ?? [];

        if (0 === count($component) && 0 === count($dimensions)) {
            return $request->withUri(
                $request->getUri()->withQuery(
                    (string) $qs->withoutParam($this->qsParam)
                )
            );
        }

        foreach ($component as $dimension) {
            if (!in_array($dimension, $dimensions)) {
                $dimensions[] = $dimension->getField();
            }
        }

        $dimensions = array_values($dimensions);

        return $request->withUri(
            $request->getUri()->withQuery(
                (string) $qs->withParam($this->qsParam, $dimensions)
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function supportsComponent(ComponentInterface $component): bool
    {
        return $component instanceof DrilldownComponentInterface;
    }
}
