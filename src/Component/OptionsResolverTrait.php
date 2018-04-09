<?php

namespace BenTools\OpenCubes\Component;

use Symfony\Component\OptionsResolver\OptionsResolver;

trait OptionsResolverTrait
{

    private static $resolversByClass = [];

    protected $options = [];

    /**
     * @return OptionsResolver
     */
    protected static function optionsResolver(): OptionsResolver
    {
        $class = get_class();

        // Was configureOptions() executed before for this class?
        if (!isset(self::$resolversByClass[$class])) {
            self::$resolversByClass[$class] = new OptionsResolver();
            self::configureOptions(self::$resolversByClass[$class]);
        }

        return self::$resolversByClass[$class];
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected static function configureOptions(OptionsResolver $resolver): void
    {
    }

    /**
     * @param array $options
     * @return self
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function withOptions(array $options): self
    {
        $clone = clone $this;
        $clone->options = self::optionsResolver()->resolve(array_replace($this->options, $options));
        return $clone;
    }
}
