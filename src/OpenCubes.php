<?php

namespace BenTools\OpenCubes;

use BenTools\OpenCubes\Component\BreakDown\BreakDownComponentFactory;
use BenTools\OpenCubes\Component\ComponentFactoryInterface;
use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Filter\FilterComponentFactory;
use BenTools\OpenCubes\Component\Pager\PagerComponentFactory;
use BenTools\OpenCubes\Component\Sort\SortComponentFactory;
use Psr\Http\Message\UriInterface;

final class OpenCubes
{
    /**
     * @var ComponentFactoryInterface[]
     */
    private $factories = [];

    /**
     * OpenCubes constructor.
     * @param iterable $factories
     */
    public function __construct(iterable $factories = [])
    {
        foreach ($factories as $factory) {
            $this->registerFactory($factory);
        }
    }

    /**
     * @param ComponentFactoryInterface $factory
     */
    public function registerFactory(ComponentFactoryInterface $factory): void
    {
        array_unshift($this->factories, $factory);
    }

    /**
     * @param string            $componentName
     * @param array             $options
     * @param UriInterface|null $uri
     * @return ComponentInterface
     * @throws \RuntimeException
     */
    public function getComponent(string $componentName, array $options = [], UriInterface $uri = null): ComponentInterface
    {
        $uri = $uri ?? current_location();

        foreach ($this->factories as $factory) {
            if ($factory->supports($componentName)) {
                return $factory->createComponent($uri, $options);
            }
        }

        throw new \RuntimeException(sprintf('Component "%s" could not be instanciated.', $componentName));
    }

    /**
     * Instanciate with default configuration.
     *
     * @return OpenCubes
     */
    public static function create(): self
    {
        return new self([
            new PagerComponentFactory(),
            new SortComponentFactory(),
            new FilterComponentFactory(),
            new BreakDownComponentFactory(),
        ]);
    }
}
