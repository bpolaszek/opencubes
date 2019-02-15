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
        PagerUriManagerInterface $pagerUriManager = null,
        SortUriManagerInterface $sortUriManager = null,
        FilterUriManagerInterface $filterUriManager = null,
        BreakDownUriManagerInterface $breakDownUriManager = null
    ): self {
        $pagerUriManager = $pagerUriManager ?? new PagerUriManager($options[PagerComponent::getName() . '_uri'] ?? []);
        $sortUriManager = $sortUriManager ?? new SortUriManager($options[SortComponent::getName() . '_uri'] ?? [], $pagerUriManager);
        $filterUriManager = $filterUriManager ?? new FilterUriManager($options[FilterComponent::getName() . '_uri'] ?? [], $pagerUriManager);
        $breakDownUriManager = $breakDownUriManager ?? new BreakDownUriManager($options[BreakDownComponent::getName() . '_uri'] ?? [], $pagerUriManager, $sortUriManager);
        return new self([
            new PagerComponentFactory($options[PagerComponent::getName()] ?? [], $pagerUriManager),
            new SortComponentFactory($options[SortComponent::getName()] ?? [], $sortUriManager),
            new FilterComponentFactory($filterUriManager),
            new BreakDownComponentFactory($options[BreakDownComponent::getName()] ?? [], $breakDownUriManager),
        ]);
    }
}
