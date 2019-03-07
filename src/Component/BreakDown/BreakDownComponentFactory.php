<?php

namespace BenTools\OpenCubes\Component\BreakDown;

use BenTools\OpenCubes\Component\BreakDown\Model\Group;
use BenTools\OpenCubes\Component\ComponentFactoryInterface;
use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\OptionsTrait;
use Psr\Http\Message\UriInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BreakDownComponentFactory implements ComponentFactoryInterface
{
    use OptionsTrait;
    
    public const OPT_AVAILABLE_GROUPS = 'available_groups';
    public const OPT_DEFAULT_GROUPS = 'default_groups';
    public const OPT_APPLIED_GROUPS = 'applied_groups';
    public const OPT_ENABLE_MULTIGROUP = 'enable_multiple_groups';

    /**
     * @var BreakDownUriManagerInterface
     */
    private $uriManager;

    /**
     * BreakDownComponentFactory constructor.
     * @param array                             $options
     * @param BreakDownUriManagerInterface|null $uriManager
     */
    public function __construct(array $options = [], BreakDownUriManagerInterface $uriManager = null)
    {
        $this->options = $options;
        $this->uriManager = $uriManager ?? new BreakDownUriManager();
    }

    /**
     * @inheritDoc
     */
    public function supports(string $name): bool
    {
        return BreakDownComponent::getName() === $name;
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
            self::OPT_AVAILABLE_GROUPS  => [],
            self::OPT_DEFAULT_GROUPS    => [],
            self::OPT_APPLIED_GROUPS    => [],
            self::OPT_ENABLE_MULTIGROUP => true,
        ]);

        $resolver->setAllowedTypes(self::OPT_AVAILABLE_GROUPS, 'array');
        $resolver->setAllowedTypes(self::OPT_DEFAULT_GROUPS, 'array');
        $resolver->setAllowedTypes(self::OPT_APPLIED_GROUPS, 'array');
        $resolver->setAllowedTypes(self::OPT_ENABLE_MULTIGROUP, 'bool');

        return $resolver->resolve($options);
    }

    /**
     * @inheritDoc
     * @return BreakDownComponent
     */
    public function createComponent(UriInterface $uri, array $options = []): ComponentInterface
    {
        $options = $this->resolveOptions(
            $this->getMergedOptions($options, [
                self::OPT_APPLIED_GROUPS => $this->uriManager->getAppliedGroups($uri),
            ])
        );

        $rawAppliedGroups = [] !== $this->getOption(self::OPT_APPLIED_GROUPS, $options) ? $this->getOption(self::OPT_APPLIED_GROUPS, $options) : $this->getOption(self::OPT_DEFAULT_GROUPS, $options);
        $rawAvailableGroups = $this->getOption(self::OPT_AVAILABLE_GROUPS, $options);
        $appliedGroups = [];
        $availableGroups = [];


        // Add to available if necessary
        foreach ($rawAppliedGroups as $field) {
            if (!array_key_exists($field, $rawAvailableGroups)) {
                $rawAvailableGroups[] = $field;
            }
        }

        // Create URIs
        foreach ($rawAppliedGroups as $f => $field) {
            $stack = $rawAppliedGroups;
            unset($stack[$f]);
            $stack = array_values($stack);

            $uri = $this->uriManager->buildGroupUri($uri, $stack);
            $appliedGroups[] = new Group($field, true, $uri);
        }


        // Cleanup
        foreach ($rawAvailableGroups as $f => $field) {
            if (in_array($field, $rawAppliedGroups)) {
                unset($rawAvailableGroups[$f]);
            }
        }
        $rawAvailableGroups = array_values($rawAvailableGroups);

        // Generate available groups
        foreach ($rawAvailableGroups as $field) {
            if (true === $this->getOption(self::OPT_ENABLE_MULTIGROUP, $options)) {
                $stack = $rawAppliedGroups;
                $stack[] = $field;
            } else {
                $stack = [$field];
            }
            $uri = $this->uriManager->buildGroupUri($uri, $stack);
            $availableGroups[] = new Group($field, false, $uri);
        }


        return new BreakDownComponent(array_merge($appliedGroups, $availableGroups));
    }
}
