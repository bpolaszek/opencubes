<?php

namespace BenTools\OpenCubes\GangnamStyle\Sort;

use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Sort\SortComponentInterface;
use BenTools\OpenCubes\Component\Sort\SortInterface;
use BenTools\OpenCubes\Request\RequestBuilderInterface;
use BenTools\QueryString\Parser\QueryStringParserInterface;
use BenTools\QueryString\Renderer\ArrayValuesNormalizerRenderer;
use BenTools\QueryString\Renderer\QueryStringRendererInterface;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Psr\Http\Message\RequestInterface;
use function BenTools\QueryString\query_string;
use function BenTools\UriFactory\Helper\current_location;

final class SortRequestBuilder implements RequestBuilderInterface
{
    /**
     * @var string
     */
    private $qsParam;

    /**
     * @var MessageFactory
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
     * SortRequestBuilder constructor.
     * @param string                          $queryStringParam
     * @param MessageFactory|null             $requestFactory
     * @param QueryStringParserInterface|null $queryStringParser
     * @throws \Http\Discovery\Exception\NotFoundException
     */
    public function __construct(
        string $queryStringParam,
        MessageFactory $requestFactory = null,
        QueryStringParserInterface $queryStringParser = null
    ) {
        $this->qsParam = $queryStringParam;
        $this->requestFactory = $requestFactory ?? MessageFactoryDiscovery::find();
        $this->queryStringParser = $queryStringParser;
        $this->queryStringRenderer = ArrayValuesNormalizerRenderer::factory();
    }

    /**
     * @inheritDoc
     */
    public function supportsComponent(ComponentInterface $component): bool
    {
        return $component instanceof SortComponentInterface;
    }

    /**
     * @param SortComponentInterface $component
     * @param RequestInterface|null  $request
     * @return RequestInterface
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function buildRequest(ComponentInterface $component, RequestInterface $request = null): RequestInterface
    {
        $request = $request ?? $this->requestFactory->createRequest('GET', current_location());
        $qs = query_string($request->getUri(), $this->queryStringParser)->withRenderer($this->queryStringRenderer);
        $sorts = $qs->getParam($this->qsParam) ?? [];

        if (0 === count($component->getSorts()) && 0 === count($sorts)) {
            return $request->withUri(
                $request->getUri()->withQuery(
                    (string) $qs->withoutParam($this->qsParam)
                )
            );
        }

        foreach ($component->getSorts() as $sort) {
            $sorts[$sort->getField()] = $this->humanizeDirection($sort);
        }

        return $request->withUri(
            $request->getUri()->withQuery(
                (string) $qs->withParam($this->qsParam, $sorts)
            )
        );
    }

    /**
     * @param SortInterface $sort
     * @return string
     */
    private function humanizeDirection(SortInterface $sort): string
    {
        return $sort->isAsc() ? 'asc' : 'desc';
    }
}
