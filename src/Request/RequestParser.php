<?php

namespace BenTools\OpenCubes\Request;

use BenTools\OpenCubes\Component\ComponentInterface;
use Psr\Http\Message\RequestInterface;

final class RequestParser
{
    /**
     * @var RequestParserInterface[]
     */
    private $requestParsers;

    /**
     * @var ComponentInterface[]
     */
    private $components;

    /**
     * RequestParser constructor.
     * @param array $requestParsers
     * @param array $components
     */
    public function __construct(array $requestParsers = [], array $components = [])
    {
        $this->requestParsers = (function (RequestParserInterface ...$requestParsers) {
            return $requestParsers;
        })(...$requestParsers);

        $this->components = (function (ComponentInterface ...$components) {
            return $components;
        })(...$components);
    }

    /**
     * @param RequestInterface $request
     * @return ComponentInterface[]
     */
    public function getComponents(RequestInterface $request): iterable
    {
        foreach ($this->requestParsers as $requestParser) {
            if ($this->hasComponent($requestParser)) {
                foreach ($this->getMatchingComponents($requestParser) as $component) {
                    $component = $requestParser->parseRequest($request, $component);
                    yield $component->getName() => $component;
                }
            } else {
                $component = $requestParser->parseRequest($request);
                yield $component->getName() => $component;
            }
        }
    }

    /**
     * @param RequestParserInterface $requestParser
     * @return bool
     */
    private function hasComponent(RequestParserInterface $requestParser): bool
    {
        foreach ($this->components as $component) {
            if ($requestParser->supportsComponent($component)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param RequestParserInterface $requestParser
     * @return ComponentInterface[]
     */
    private function getMatchingComponents(RequestParserInterface $requestParser): iterable
    {
        foreach ($this->components as $component) {
            if ($requestParser->supportsComponent($component)) {
                yield $component;
            }
        }
    }
}
