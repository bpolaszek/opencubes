<?php

namespace BenTools\OpenCubes\Request;

use BenTools\OpenCubes\Component\ComponentInterface;
use Psr\Http\Message\RequestInterface;

final class RequestBuilder
{
    /**
     * @var RequestBuilderInterface[]
     */
    private $requestBuilders;

    /**
     * RequestBuilder constructor.
     * @param RequestBuilderInterface[] $requestBuilders
     */
    public function __construct(array $requestBuilders = [])
    {
        $this->requestBuilders = (function (RequestBuilderInterface ...$requestBuilders) {
            return $requestBuilders;
        })(...$requestBuilders);
    }

    /**
     * @param RequestInterface     $request
     * @param ComponentInterface[] ...$components
     * @return RequestInterface
     */
    public function buildRequest(RequestInterface $request, ComponentInterface ...$components): RequestInterface
    {
        foreach ($components as $component) {
            foreach ($this->requestBuilders as $requestBuilder) {
                if ($requestBuilder->supportsComponent($component)) {
                    $request = $requestBuilder->buildRequest($component, $request);
                }
            }
        }

        return $request;
    }
}
