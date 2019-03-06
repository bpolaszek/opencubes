<?php

namespace BenTools\OpenCubes;

use BenTools\OpenCubes\Component\BreakDown\BreakDownComponent;
use BenTools\OpenCubes\Component\BreakDown\BreakDownComponentFactory;
use BenTools\OpenCubes\Component\BreakDown\BreakDownUriManager;
use BenTools\OpenCubes\Component\BreakDown\BreakDownUriManagerInterface;
use BenTools\OpenCubes\Component\ComponentFactoryInterface;
use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Filter\FilterComponent;
use BenTools\OpenCubes\Component\Filter\FilterComponentFactory;
use BenTools\OpenCubes\Component\Filter\FilterUriManager;
use BenTools\OpenCubes\Component\Filter\FilterUriManagerInterface;
use BenTools\OpenCubes\Component\Pager\PagerComponent;
use BenTools\OpenCubes\Component\Pager\PagerComponentFactory;
use BenTools\OpenCubes\Component\Pager\PagerUriManager;
use BenTools\OpenCubes\Component\Pager\PagerUriManagerInterface;
use BenTools\OpenCubes\Component\Sort\SortComponent;
use BenTools\OpenCubes\Component\Sort\SortComponentFactory;
use BenTools\OpenCubes\Component\Sort\SortUriManager;
use BenTools\OpenCubes\Component\Sort\SortUriManagerInterface;
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
     * @param array                             $options
     * @param PagerUriManagerInterface|null     $pagerUriManager
     * @param SortUriManagerInterface|null      $sortUriManager
     * @param FilterUriManagerInterface|null    $filterUriManager
     * @param BreakDownUriManagerInterface|null $breakDownUriManager
     * @return OpenCubes
     */
    public static function create(
        array $options = [],
        ...$args
    ): self {

        $pagerUriManager = (function (array $args) use ($options): PagerUriManagerInterface {
            foreach ($args as $arg) {
                if ($arg instanceof PagerUriManagerInterface) {
                    return $arg;
                }
            }

            return new PagerUriManager($options[PagerComponent::getName() . '_uri'] ?? []);
        })($args);

        $sortUriManager = (function (array $args) use ($options, $pagerUriManager): SortUriManagerInterface {
            foreach ($args as $arg) {
                if ($arg instanceof SortUriManagerInterface) {
                    return $arg;
                }
            }

            return new SortUriManager($options[SortComponent::getName() . '_uri'] ?? [], $pagerUriManager);
        })($args);

        $filterUriManager = (function (array $args) use ($options, $pagerUriManager): FilterUriManagerInterface {
            foreach ($args as $arg) {
                if ($arg instanceof FilterUriManagerInterface) {
                    return $arg;
                }
            }

            return new FilterUriManager($options[FilterComponent::getName() . '_uri'] ?? [], $pagerUriManager);
        })($args);

        $breakDownUriManager = (function (array $args) use ($options, $pagerUriManager, $sortUriManager): BreakDownUriManagerInterface {
            foreach ($args as $arg) {
                if ($arg instanceof BreakDownUriManagerInterface) {
                    return $arg;
                }
            }

            return new BreakDownUriManager($options[BreakDownComponent::getName() . '_uri'] ?? [], $pagerUriManager, $sortUriManager);
        })($args);

        return new self([
            new PagerComponentFactory($options[PagerComponent::getName()] ?? [], $pagerUriManager),
            new SortComponentFactory($options[SortComponent::getName()] ?? [], $sortUriManager),
            new FilterComponentFactory($options[FilterComponent::getName()] ?? [], $filterUriManager),
            new BreakDownComponentFactory($options[BreakDownComponent::getName()] ?? [], $breakDownUriManager),
        ]);
    }
}
