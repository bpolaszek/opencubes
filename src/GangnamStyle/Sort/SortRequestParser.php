<?php

namespace BenTools\OpenCubes\GangnamStyle\Sort;

use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Sort\Sort;
use BenTools\OpenCubes\Component\Sort\SortComponent;
use BenTools\OpenCubes\Component\Sort\SortComponentInterface;
use BenTools\OpenCubes\Component\Sort\SortInterface;
use BenTools\OpenCubes\Request\RequestParserInterface;
use BenTools\QueryString\Parser\QueryStringParserInterface;
use BenTools\QueryString\Renderer\ArrayValuesNormalizerRenderer;
use BenTools\QueryString\Renderer\QueryStringRendererInterface;
use Psr\Http\Message\RequestInterface;
use function BenTools\QueryString\query_string;

final class SortRequestParser implements RequestParserInterface
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
     * SortRequestParser constructor.
     * @param string                            $queryStringParam
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
        return $component instanceof SortComponentInterface;
    }

    /**
     * @param RequestInterface            $request
     * @param SortComponentInterface|null $component
     * @return SortComponentInterface
     * @throws \InvalidArgumentException
     */
    public function parseRequest(RequestInterface $request, ComponentInterface $component = null): ComponentInterface
    {
        $component = $component ?? new SortComponent();
        $qs = query_string($request->getUri(), $this->queryStringParser)->withRenderer($this->queryStringRenderer);
        $values = $qs->getParam($this->qsParam) ?? [];

        $sorts = [];
        foreach ($values as $field => $direction) {
            $sorts[] = new Sort($field, $this->parseDirection($direction, $field));
        }

        return $component->withAddedSort(...$sorts);
    }

    /**
     * @param string $direction
     * @param string $field
     * @return int
     * @throws \InvalidArgumentException
     */
    private function parseDirection(string $direction, string $field): int
    {
        switch (trim($direction)) {
            case SORT_ASC:
            case 'asc':
            case 'ASC':
            case '':
                return SortInterface::SORT_ASC;

            case SORT_DESC:
            case 'desc':
            case 'DESC':
                return SortInterface::SORT_DESC;
        }

        throw new \InvalidArgumentException(sprintf('Invalid direction %s for field %s', $direction, $field));
    }
}
