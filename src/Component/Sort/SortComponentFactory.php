<?php

namespace BenTools\OpenCubes\Component\Sort;

use BenTools\OpenCubes\Component\ComponentFactoryInterface;
use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Sort\Model\Sort;
use BenTools\OpenCubes\OptionsTrait;
use Psr\Http\Message\UriInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SortComponentFactory implements ComponentFactoryInterface
{
    use OptionsTrait;

    public const OPT_AVAILABLE_SORTS = 'available_sorts';
    public const OPT_DEFAULT_SORTS = 'default_sorts';
    public const OPT_APPLIED_SORTS = 'applied_sorts';
    public const OPT_ENABLE_MULTISORT = 'enable_multisort';

    /**
     * @var SortUriManagerInterface
     */
    private $uriManager;

    /**
     * SortComponentFactory constructor.
     * @param array                        $options
     * @param SortUriManagerInterface|null $uriManager
     */
    public function __construct(array $options = [], SortUriManagerInterface $uriManager = null)
    {
        $this->options = $this->resolveOptions($options);
        $this->uriManager = $uriManager ?? new SortUriManager();
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
            self::OPT_AVAILABLE_SORTS  => [],
            self::OPT_DEFAULT_SORTS    => [],
            self::OPT_APPLIED_SORTS    => [],
            self::OPT_ENABLE_MULTISORT => false,
        ]);

        $resolver->setAllowedTypes(self::OPT_AVAILABLE_SORTS, 'array');
        $resolver->setAllowedTypes(self::OPT_DEFAULT_SORTS, 'array');
        $resolver->setAllowedTypes(self::OPT_APPLIED_SORTS, 'array');
        $resolver->setAllowedTypes(self::OPT_ENABLE_MULTISORT, 'bool');

        return $resolver->resolve($options);
    }

    /**
     * @inheritDoc
     */
    public function supports(string $name): bool
    {
        return SortComponent::getName() === $name;
    }

    /**
     * @inheritDoc
     */
    public function createComponent(UriInterface $uri, array $options = []): ComponentInterface
    {
        $options = $this->resolveOptions(
            $this->getMergedOptions($options, [
                self::OPT_APPLIED_SORTS => $this->uriManager->getAppliedSorts($uri),
            ])
        );

        $appliedSorts = [] !== $this->getOption(self::OPT_APPLIED_SORTS, $options) ? $this->getOption(self::OPT_APPLIED_SORTS, $options) : $this->getOption(self::OPT_DEFAULT_SORTS, $options);
        $availableSorts = $this->getOption(self::OPT_AVAILABLE_SORTS, $options);

        $sorts = [];

        foreach ($availableSorts as $field => $directions) {
            foreach ($directions as $direction) {
                if (isset($appliedSorts[$field]) && $direction === $appliedSorts[$field]) {
                    $stack = true === $this->getOption(self::OPT_ENABLE_MULTISORT, $options) ? array_replace($appliedSorts, [$field => $direction]) : [$field => $direction];
                    unset($stack[$field]);
                    $unsetUri = $this->uriManager->buildSortUri($uri, $stack);
                    $sorts[] = new Sort($field, $direction, true, $unsetUri);
                    continue;
                }
                $stack = true === $this->getOption(self::OPT_ENABLE_MULTISORT) ? array_replace($appliedSorts, [$field => $direction]) : [$field => $direction];
                $sorts[] = new Sort($field, $direction, false, $this->uriManager->buildSortUri($uri, $stack));
            }
        }

        foreach ($appliedSorts as $field => $direction) {
            if (!array_key_exists($field, $availableSorts) || !in_array($direction, $availableSorts[$field])) {
                $stack = true === $this->getOption(self::OPT_ENABLE_MULTISORT, $options) ? array_replace($appliedSorts, [$field => $direction]) : [$field => $direction];
                unset($stack[$field]);
                $unsetUri = $this->uriManager->buildSortUri($uri, $stack);
                $sorts[] = new Sort($field, $direction, true, $unsetUri);
            }
        }

        return new SortComponent($sorts);
    }
}
