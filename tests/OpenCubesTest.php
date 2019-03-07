<?php

namespace BenTools\OpenCubes\Tests;

use BenTools\OpenCubes\Component\BreakDown\BreakDownComponent;
use BenTools\OpenCubes\Component\BreakDown\BreakDownComponentFactory;
use BenTools\OpenCubes\Component\BreakDown\BreakDownUriManager;
use BenTools\OpenCubes\Component\Filter\FilterComponent;
use BenTools\OpenCubes\Component\Filter\FilterUriManager;
use BenTools\OpenCubes\Component\Pager\PagerComponent;
use BenTools\OpenCubes\Component\Pager\PagerComponentFactory;
use BenTools\OpenCubes\Component\Pager\PagerUriManager;
use BenTools\OpenCubes\Component\Sort\SortComponent;
use BenTools\OpenCubes\Component\Sort\SortComponentFactory;
use BenTools\OpenCubes\Component\Sort\SortUriManager;
use BenTools\OpenCubes\OpenCubes;
use PHPUnit\Framework\TestCase;
use function BenTools\OpenCubes\first_of;
use function BenTools\OpenCubes\stringify_uri;
use function BenTools\UriFactory\Helper\uri;

class OpenCubesTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_create_components_without_any_configuration()
    {
        $openCubes = OpenCubes::create();
        $this->assertInstanceOf(PagerComponent::class, $openCubes->getComponent(PagerComponent::getName()));
        $this->assertInstanceOf(FilterComponent::class, $openCubes->getComponent(FilterComponent::getName()));
        $this->assertInstanceOf(SortComponent::class, $openCubes->getComponent(SortComponent::getName()));
        $this->assertInstanceOf(BreakDownComponent::class, $openCubes->getComponent(BreakDownComponent::getName()));
    }

    /**
     * @test
     */
    public function it_can_create_components_with_options()
    {
        $openCubes = OpenCubes::create([
            'pager' => [
                PagerComponentFactory::OPT_TOTAL_ITEMS => 200,
            ],
            'pager_uri' => [
                PagerUriManager::OPT_PAGE_QUERY_PARAM => 'p',
            ],
            'sort' => [
                SortComponentFactory::OPT_AVAILABLE_SORTS => ['date' => ['asc', 'desc']],
            ],
            'sort_uri' => [
                SortUriManager::OPT_SORT_QUERY_PARAM => 'order',
            ],
            'breakdown' => [
                 BreakDownComponentFactory::OPT_AVAILABLE_GROUPS => ['dummy'],
            ],
            'breakdown_uri' => [
                BreakDownUriManager::OPT_BREAKDOWN_QUERY_PARAM => 'group',
            ],
            'filter_uri' => [
                FilterUriManager::OPT_FILTER_QUERY_PARAM => 'query',
            ],
        ]);

        $uri = uri('https://example.org/?foo=bar&query[bar]=foo');

        /**
         * @var PagerComponent $pager
         * @var SortComponent $sorting
         * @var FilterComponent $filters
         * @var BreakDownComponent $breakdown
         */
        $pager = $openCubes->getComponent(PagerComponent::getName(), [], $uri);
        $sorting = $openCubes->getComponent(SortComponent::getName(), [], $uri);
        $filters = $openCubes->getComponent(FilterComponent::getName(), [], $uri);
        $breakdown = $openCubes->getComponent(BreakDownComponent::getName(), [], $uri);

        $this->assertEquals(200, $pager->getNbItems());
        $this->assertEquals('https://example.org/?foo=bar&query[bar]=foo&p=3', stringify_uri($pager->getPages()[2]->getUri()));

        $this->assertTrue($sorting->has('date'));
        $this->assertEquals('https://example.org/?foo=bar&query[bar]=foo&order[date]=desc', stringify_uri(first_of($sorting->get('date', 'desc'))->getToggleUri()));

        $this->assertEquals('https://example.org/?foo=bar', stringify_uri($filters->get('bar')->getToggleUri()));

        $this->assertTrue($breakdown->has('dummy'));
        $this->assertEquals('https://example.org/?foo=bar&query[bar]=foo&group[]=dummy', stringify_uri($breakdown->get('dummy')->getToggleUri()));
    }

    /**
     * @test
     */
    public function it_can_create_components_with_registered_uri_managers()
    {
        $pagerUriManager = new PagerUriManager([
            PagerUriManager::OPT_PAGE_QUERY_PARAM => 'p',
        ]);

        $sortUriManager = new SortUriManager([
            SortUriManager::OPT_SORT_QUERY_PARAM => 'order',
        ]);

        $openCubes = OpenCubes::create([
            'pager' => [
                PagerComponentFactory::OPT_TOTAL_ITEMS => 200,
            ],
            'sort' => [
                SortComponentFactory::OPT_AVAILABLE_SORTS => ['date' => ['asc', 'desc']],
            ],
            'breakdown' => [
                BreakDownComponentFactory::OPT_AVAILABLE_GROUPS => ['dummy'],
            ],
        ], $pagerUriManager, $sortUriManager);

        $uri = uri('https://example.org/?foo=bar&query[bar]=foo&order[date]=asc');

        /**
         * @var PagerComponent $pager
         * @var SortComponent $sorting
         * @var FilterComponent $filters
         * @var BreakDownComponent $breakdown
         */
        $pager = $openCubes->getComponent(PagerComponent::getName(), [], $uri);
        $sorting = $openCubes->getComponent(SortComponent::getName(), [], $uri);
        $breakdown = $openCubes->getComponent(BreakDownComponent::getName(), [], $uri);

        $this->assertEquals(200, $pager->getNbItems());
        $this->assertEquals('https://example.org/?foo=bar&query[bar]=foo&order[date]=asc&p=3', stringify_uri($pager->getPages()[2]->getUri()));

        $this->assertTrue($sorting->has('date'));
        $this->assertEquals('https://example.org/?foo=bar&query[bar]=foo&order[date]=desc', stringify_uri(first_of($sorting->get('date', 'desc'))->getToggleUri()));

        $this->assertTrue($breakdown->has('dummy'));
        $this->assertEquals('https://example.org/?foo=bar&query[bar]=foo&breakdown[]=dummy', stringify_uri($breakdown->get('dummy')->getToggleUri()));
    }

}
