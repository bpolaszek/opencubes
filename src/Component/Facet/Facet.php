<?php

namespace BenTools\OpenCubes\Component\Facet;

use BenTools\OpenCubes\Component\OptionsResolverTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Facet implements FacetInterface
{
    use OptionsResolverTrait;

    /**
     * Facet constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = self::optionsResolver()->resolve($options);
    }

    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    protected static function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'availableValues' => [],
            'allowsMultipleValues' => false,
        ]);
        $resolver->setRequired(['field']);
        $resolver->setAllowedTypes('availableValues', FacetValueInterface::class . '[]');
        $resolver->setAllowedTypes('allowsMultipleValues', 'bool');
    }

    /**
     * @inheritDoc
     */
    public function getField(): string
    {
        return $this->options['field'];
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return $this->options['label'] ?? $this->getField();
    }

    /**
     * @inheritDoc
     */
    public function getAvailableValues(): iterable
    {
        return $this->options['availableValues'];
    }

    /**
     * @inheritDoc
     */
    public function allowsMultipleValues(): bool
    {
        return $this->options['allowsMultipleValues'];
    }

    /**
     * @param $availableValues
     * @return $this
     */
    public function withAvailableValues($availableValues): self
    {
        return $this->withOptions(['availableValues' => $availableValues]);
    }
}
