<?php

namespace BenTools\OpenCubes\Component\BreakDown\Model;

use Psr\Http\Message\UriInterface;
use function BenTools\OpenCubes\stringify_uri;

final class Group implements \JsonSerializable
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var bool
     */
    private $applied;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * Group constructor.
     * @param string       $field
     * @param bool         $applied
     * @param UriInterface $uri
     */
    public function __construct(string $field, bool $applied, UriInterface $uri = null)
    {
        $this->field = $field;
        $this->applied = $applied;
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
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
    public function getUri(): ?UriInterface
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $output = [
            'field' => $this->field,
            'is_applied' => $this->applied,
        ];

        if (!$this->isApplied()) {
            $output['link'] = stringify_uri($this->uri);
        } else {
            $output['unset_link'] = stringify_uri($this->uri);
        }

        return $output;
    }
}
