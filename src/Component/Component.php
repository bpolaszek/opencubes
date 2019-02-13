<?php

namespace BenTools\OpenCubes\Component;

use BenTools\OpenCubes\OptionsTrait;
use Psr\Http\Message\UriInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Component
 * @package BenTools\OpenCubes\Component
 * @deprecated
 */
abstract class Component implements \JsonSerializable
{
    use OptionsTrait;

    /**
     * @var OptionsResolver
     */
    protected $resolver;

    /**
     * @var UriInterface
     */
    private $baseUri;

    /**
     * @var bool
     */
    protected $compiled = false;

    /**
     * Component constructor.
     * @param array $options
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function __construct(array $options = [])
    {
        $this->resolver = new OptionsResolver();
        $this->configureResolver($this->resolver);
        $this->options = $this->resolver->resolve($options);
    }

    /**
     * Return the component's name.
     * @return string
     */
    abstract public static function getName(): string;

    /**
     * Compile component from options.
     */
    abstract public function compile(): void;

    /**
     * Hydrate component options from the application.
     *
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        if (true === $this->compiled) {
            throw new \LogicException(sprintf("Component %s is already compiled.", $this->getName()));
        }
        $options = array_diff($options, array_filter($options, 'is_null'));
        $this->options = $this->resolver->resolve(array_replace($this->options, $options));
    }

    /**
     * @param UriInterface $baseUri
     */
    final public function setBaseUri(?UriInterface $baseUri): void
    {
        if (null === $baseUri) {
            return;
        }
        $this->baseUri = $baseUri;
        $this->setOptionsFromUri($baseUri);
    }

    /**
     * @return UriInterface
     */
    public function getBaseUri(): ?UriInterface
    {
        return $this->baseUri;
    }

    /**
     * Hydrate component options from the given Uri.
     *
     * @param UriInterface $uri
     * @deprecated
     */
    abstract protected function setOptionsFromUri(UriInterface $uri): void;
    /**
     * Configure the option resolver for this component.
     *
     * @return array
     * @deprecated
     */
    abstract protected function configureResolver(OptionsResolver $resolver): void;

    /**
     * Compile the component from its options.
     */

    /**
     * Return a normalized version of the component with the correct URIs.
     *
     * @inheritDoc
     */
    abstract public function jsonSerialize(): array;
}
