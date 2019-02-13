<?php

namespace BenTools\OpenCubes;

use Symfony\Component\OptionsResolver\Exception\NoSuchOptionException;

trait OptionsTrait
{
    protected $options = [];

    /**
     * @param string $key
     * @return mixed
     * @throws NoSuchOptionException
     */
    public function getOption(string $key, array $options = null)
    {
        $options = $options ?? $this->options;
        if (!array_key_exists($key, $options)) {
            throw new NoSuchOptionException(sprintf('Option %s does not exist.', $key));
        }

        return $options[$key];
    }

    /**
     * @param array $options
     * @return array
     */
    public function getMergedOptions(array ...$options): array
    {
        return array_replace($this->options, ...$options);
    }
}
