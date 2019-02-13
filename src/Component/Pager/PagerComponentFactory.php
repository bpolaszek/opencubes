<?php

namespace BenTools\OpenCubes\Component\Pager;

use BenTools\OpenCubes\Component\ComponentFactoryInterface;
use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Pager\Model\PageSize;
use BenTools\OpenCubes\OptionsTrait;
use Psr\Http\Message\UriInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PagerComponentFactory implements ComponentFactoryInterface
{
    use OptionsTrait;

    public const OPT_DEFAULT_PAGESIZE = 'default_size';
    public const OPT_AVAILABLE_PAGESIZES = 'available_sizes';
    public const OPT_PAGESIZING_ENABLED = 'pagesizing_enabled';
    public const OPT_TOTAL_ITEMS = 'total_items';
    public const OPT_DELTA = 'delta';

    /**
     * @var PagerUriManagerInterface
     */
    private $uriManager;

    /**
     * PagerComponentFactory constructor.
     * @param array                $options
     * @param PagerUriManagerInterface|null $uriManager
     */
    public function __construct(array $options = [], PagerUriManagerInterface $uriManager = null)
    {
        $this->options = $this->resolveOptions($options);
        $this->uriManager = $uriManager ?? new PagerUriManager();
    }

    /**
     * @param array $options
     * @return array
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    protected function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            self::OPT_TOTAL_ITEMS  => 0,
            self::OPT_DEFAULT_PAGESIZE    => 50,
            self::OPT_AVAILABLE_PAGESIZES => [],
            self::OPT_PAGESIZING_ENABLED  => true,
            self::OPT_DELTA  => null,
        ]);

        $resolver->setAllowedTypes(self::OPT_TOTAL_ITEMS, ['int']);
        $resolver->setAllowedTypes(self::OPT_AVAILABLE_PAGESIZES, 'int[]');
        $resolver->setAllowedTypes(self::OPT_DEFAULT_PAGESIZE, ['int']);
        $resolver->setAllowedTypes(self::OPT_PAGESIZING_ENABLED, 'bool');
        $resolver->setAllowedTypes(self::OPT_DELTA, ['null', 'int']);
        $resolver->setRequired(self::OPT_PAGESIZING_ENABLED);

        return $resolver->resolve($options);
    }

    /**
     * @inheritDoc
     */
    public function supports(string $name): bool
    {
        return PagerComponent::getName() === $name;
    }

    /**
     * @inheritDoc
     */
    public function createComponent(UriInterface $uri, array $options = []): ComponentInterface
    {
        $options = $this->resolveOptions($this->getMergedOptions($options));

        if (false === $this->getOption(self::OPT_PAGESIZING_ENABLED, $options)) {
            return new PagerComponent($uri);
        }

        $currentSize = $this->uriManager->getCurrentPageSize($uri) ?? $this->getOption(self::OPT_DEFAULT_PAGESIZE, $options);
        $currentPageNumber = $this->uriManager->getCurrentPageNumber($uri) ?? 1;
        $availableSizes = $this->getOption(self::OPT_AVAILABLE_PAGESIZES, $options);
        $totalItems = $this->getOption(self::OPT_TOTAL_ITEMS, $options);
        $delta = $this->getOption(self::OPT_DELTA, $options);

        if (!in_array($currentSize, $availableSizes)) {
            $availableSizes[] = $currentSize;
        }

        sort($availableSizes, SORT_NUMERIC);

        // Create PageSize objects
        $pageSizes = array_map(function (int $size) use ($uri, $currentSize) {
            return new PageSize($size, $size === $currentSize, $this->uriManager->buildSizeUri($uri, $size === $currentSize ? null : $size));
        }, $availableSizes);
        
        return new PagerComponent(
            $uri,
            $totalItems,
            $currentSize,
            $currentPageNumber,
            $delta,
            $pageSizes,
            $this->uriManager
        );
    }
}
