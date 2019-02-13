<?php

namespace BenTools\OpenCubes\Component\Pager;

use BenTools\OpenCubes\OptionsTrait;
use Psr\Http\Message\UriInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function BenTools\OpenCubes\cast;
use function BenTools\QueryString\query_string;

final class PagerUriManager implements PagerUriManagerInterface
{
    use OptionsTrait;

    public const OPT_PAGESIZE_QUERY_PARAM = 'pagesize_query_param';
    public const OPT_PAGE_QUERY_PARAM = 'page_query_param';

    /**
     * PageSizerUriParser constructor.
     */
    public function __construct(array $options = [])
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefaults([
            self::OPT_PAGESIZE_QUERY_PARAM => 'per_page',
            self::OPT_PAGE_QUERY_PARAM => 'page',
        ]);

        $this->options = $optionsResolver->resolve($options);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPageSize(UriInterface $uri): ?int
    {
        $qs = query_string($uri);

        return cast($qs->getParam($this->getOption(self::OPT_PAGESIZE_QUERY_PARAM)))->asIntOrNull();
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPageNumber(UriInterface $uri): ?int
    {
        $qs = query_string($uri);

        return cast($qs->getParam($this->getOption(self::OPT_PAGE_QUERY_PARAM)))->asIntOrNull();
    }

    /**
     * @param UriInterface $uri
     * @param int          $pageNumber
     * @param bool|null    $paginationEnabled
     * @return UriInterface
     * @throws \InvalidArgumentException
     */
    public function buildPageUri(UriInterface $uri, int $pageNumber): UriInterface
    {
        $qs = query_string($uri);

        if (1 === $pageNumber) {
            $qs = $qs->withoutParam($this->getOption(self::OPT_PAGE_QUERY_PARAM));
        } else {
            $qs = $qs->withParam($this->getOption(self::OPT_PAGE_QUERY_PARAM), $pageNumber);
        }

        return $uri->withQuery((string) $qs);
    }

    /**
     * @param UriInterface $uri
     * @param int          $size
     * @return UriInterface
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     */
    public function buildSizeUri(UriInterface $uri, ?int $size): UriInterface
    {
        $uri = $this->buildPageUri($uri, 1);
        $qs = query_string($uri);

        if (null === $size) {
            $qs = $qs->withoutParam($this->getOption(self::OPT_PAGESIZE_QUERY_PARAM));
        } else {
            $qs = $qs->withParam($this->getOption(self::OPT_PAGESIZE_QUERY_PARAM), $size);
        }

        return $uri->withQuery((string) $qs);
    }
}
