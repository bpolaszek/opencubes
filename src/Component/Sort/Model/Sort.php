<?php

namespace BenTools\OpenCubes\Component\Sort\Model;

use Psr\Http\Message\UriInterface;
use function BenTools\OpenCubes\stringify_uri;

final class Sort implements \JsonSerializable
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $direction;

    /**
     * @var bool
     */
    private $applied;

    /**
     * @var UriInterface
     */
    private $toggleUri;

    /**
     * Sort constructor.
     * @param string            $field
     * @param string|null       $direction
     * @param bool              $applied
     * @param UriInterface|null $uri
     */
    public function __construct(string $field, ?string $direction = null, bool $applied = true, UriInterface $uri = null)
    {
        $this->field = $field;
        $this->direction = $direction;
        $this->applied = $applied;
        $this->toggleUri = $uri;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getDirection(): ?string
    {
        return $this->direction;
    }

    /**
     * @return bool
     */
    public function isApplied(): bool
    {
        return $this->applied;
    }

    /**
     * @return UriInterface
     */
    public function getToggleUri(): ?UriInterface
    {
        return $this->toggleUri;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $output = [
            'field' => $this->field,
            'direction' => $this->direction,
            'is_applied' => $this->isApplied(),
        ];

        if ($this->isApplied()) {
            $output['unset_link'] = stringify_uri($this->getToggleUri());
        } else {
            $output['link'] = stringify_uri($this->getToggleUri());
        }

        return $output;
    }
}
