<?php

namespace BenTools\OpenCubes\Component\BreakDown;

use BenTools\OpenCubes\Component\BreakDown\Model\Group;
use BenTools\OpenCubes\Component\Pager\PagerUriManager;
use BenTools\OpenCubes\Component\Pager\PagerUriManagerInterface;
use BenTools\OpenCubes\Component\Sort\SortUriManager;
use BenTools\OpenCubes\Component\Sort\SortUriManagerInterface;
use BenTools\OpenCubes\OptionsTrait;
use Psr\Http\Message\UriInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function BenTools\QueryString\query_string;

final class BreakDownUriManager implements BreakDownUriManagerInterface
{
    use OptionsTrait;

    public const OPT_BREAKDOWN_QUERY_PARAM = 'query_param';
    public const OPT_REMOVE_SORT = 'remove_sort';

    /**
     * @var PagerUriManagerInterface
     */
    private $pagerUriManager;
    /**
     * @var SortUriManagerInterface
     */
    private $sortUriManager;

    /**
     * BreakDownUriManager constructor.
     * @param array                         $options
     * @param PagerUriManagerInterface|null $pagerUriManager
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function __construct(array $options = [], PagerUriManagerInterface $pagerUriManager = null, SortUriManagerInterface $sortUriManager = null)
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefaults([
            self::OPT_BREAKDOWN_QUERY_PARAM => 'breakdown',
            self::OPT_REMOVE_SORT           => true,
        ]);

        $this->options = $optionsResolver->resolve($options);
        $this->pagerUriManager = $pagerUriManager ?? new PagerUriManager();
        $this->sortUriManager = $sortUriManager ?? new SortUriManager();
    }

    /**
     * @inheritDoc
     */
    public function getAppliedGroups(UriInterface $uri): array
    {
        $qs = query_string($uri);

        $appliedGroups = $qs->getParam($this->getOption(self::OPT_BREAKDOWN_QUERY_PARAM));

        if (!is_array($appliedGroups)) {
            return [];
        }

        return $appliedGroups;
    }

    /**
     * @inheritDoc
     */
    public function buildGroupUri(UriInterface $uri, array $groups): UriInterface
    {

        $normalizedGroups = array_map(function ($group) {
            return $group instanceof Group ? $group->getField() : $group;
        }, $groups);

        // Reset to 1st page after breaking down
        $uri = $this->pagerUriManager->buildPageUri($uri, 1);

        // Remove sort if required
        if (true === $this->getOption(self::OPT_REMOVE_SORT)) {
            $uri = $this->sortUriManager->buildSortUri($uri, []);
        }

        $qs = query_string($uri);
        $qs = [] === $groups ? $qs->withoutParam($this->getOption(self::OPT_BREAKDOWN_QUERY_PARAM)) : $qs->withParam($this->getOption(self::OPT_BREAKDOWN_QUERY_PARAM), $normalizedGroups);

        return $uri->withQuery((string) $qs);
    }
}
