<?php

namespace BenTools\OpenCubes\Component\Sort;

use BenTools\OpenCubes\Component\Pager\PagerUriManager;
use BenTools\OpenCubes\Component\Pager\PagerUriManagerInterface;
use BenTools\OpenCubes\Component\Sort\Model\Sort;
use BenTools\OpenCubes\OptionsTrait;
use Psr\Http\Message\UriInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function BenTools\QueryString\query_string;

final class SortUriManager implements SortUriManagerInterface
{
    use OptionsTrait;

    public const OPT_SORT_QUERY_PARAM = 'query_param';

    /**
     * @var PagerUriManagerInterface
     */
    private $pagerUriManager;

    /**
     * SortUriManager constructor.
     * @param array                         $options
     * @param PagerUriManagerInterface|null $pagerUriManager
     */
    public function __construct(array $options = [], PagerUriManagerInterface $pagerUriManager = null)
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefaults([
            self::OPT_SORT_QUERY_PARAM => 'sort'
        ]);

        $this->options = $optionsResolver->resolve($options);
        $this->pagerUriManager = $pagerUriManager ?? new PagerUriManager();
    }

    /**
     * @inheritDoc
     */
    public function getAppliedSorts(UriInterface $uri): array
    {
        $qs = query_string($uri);

        $sorts = $qs->getParam($this->getOption(self::OPT_SORT_QUERY_PARAM));

        if (!is_array($sorts)) {
            return [];
        }

        return $this->normalizeSorts((array) $sorts);
    }

    /**
     * @param UriInterface $uri
     * @param array        $sorts
     * @return UriInterface
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     */
    public function buildSortUri(UriInterface $uri, array $sorts): UriInterface
    {

        $normalizedSorts = [];

        foreach ($sorts as $key => $value) {
            if ($value instanceof Sort) {
                $normalizedSorts[$value->getField()] = $value->getDirection();
            } else {
                $normalizedSorts[$key] = $value;
            }
        }

        // Reset to 1st page after sorting
        $uri = $this->pagerUriManager->buildPageUri($uri, 1);
        $qs = query_string($uri);
        $qs = [] === $sorts ? $qs->withoutParam($this->getOption(self::OPT_SORT_QUERY_PARAM)) : $qs->withParam($this->getOption(self::OPT_SORT_QUERY_PARAM), $normalizedSorts);

        return $uri->withQuery((string) $qs);
    }

    /**
     * @param array $sorts
     * @return array
     */
    private function normalizeSorts(array $sorts): array
    {
        return array_map(function (string $direction) {
            switch ($direction) {
                case '':
                    return null;
                case 'asc':
                case 'ASC':
                    return 'asc';
                case 'desc':
                case 'DESC':
                    return 'desc';
            }

            return strtolower($direction);
        }, $sorts);
    }
}
