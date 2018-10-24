<?php

namespace BenTools\OpenCubes\Tests\GangnamStyle\Filter;

use BenTools\OpenCubes\Component\Filter\CollectionFilterInterface;
use BenTools\OpenCubes\Component\Filter\FilterComponent;
use BenTools\OpenCubes\Component\Filter\RangeFilter;
use BenTools\OpenCubes\Component\Filter\SimpleFilter;
use BenTools\OpenCubes\GangnamStyle\Filter\FiltersRequestParser;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use function BenTools\QueryString\query_string;
use function GuzzleHttp\Psr7\uri_for;

class FiltersRequestParserTest extends TestCase
{

    public function testBuildFilters()
    {
        $request = new Request('GET', uri_for('http://localhost/tasks?f[status][]=pending&f[status][]=reopened&f[status][]=awaiting&f[assignee]=me&o[updatedAt]=desc&f[date]=[2018-03-01 TO *]'));
        $parser = new FiltersRequestParser('f');
        $this->assertTrue($parser->supportsComponent(new FilterComponent()));

        $component = $parser->parseRequest($request);
        $this->assertTrue($component->has('status'));
        $this->assertTrue($component->has('assignee'));
        $this->assertTrue($component->has('date'));
        $this->assertFalse($component->has('updatedAt'));

        $this->assertInstanceOf(CollectionFilterInterface::class, $component->get('status'));
        $this->assertCount(3, $component->get('status'));
        $this->assertEquals(['pending', 'reopened', 'awaiting'], $component->get('status')->getValues());

        $this->assertInstanceOf(SimpleFilter::class, $component->get('assignee'));
        $this->assertEquals('me', $component->get('assignee')->getValue());

        $this->assertInstanceOf(RangeFilter::class, $component->get('date'));
    }

    /**
     * @param string      $dateFilter
     * @param null|string $expectedLeft
     * @param null|string $expectedRight
     * @dataProvider dateRangeProvider
     */
    public function testRangeFilter(string $dateFilter, ?string $expectedLeft, ?string $expectedRight)
    {
        $uri = uri_for('http://localhost');
        $uri = $uri->withQuery(
            (string) query_string($uri)->withParam('f[date]', $dateFilter)
        );
        $request = new Request('GET', $uri);
        $parser = new FiltersRequestParser('f');
        $component = $parser->parseRequest($request);
        /** @var RangeFilter $filter */
        $filter = $component->get('date');
        $this->assertEquals($expectedLeft, $filter->getLeft());
        $this->assertEquals($expectedRight, $filter->getRight());
    }

    public function dateRangeProvider()
    {
        yield ['[* TO *]', null, null];
        yield ['[2018-01-01 TO *]', '2018-01-01', null];
        yield ['[* TO 2018-12-31]', null, '2018-12-31'];
        yield ['[2018-01-01 TO 2018-12-31 23:59:59]', '2018-01-01', '2018-12-31 23:59:59'];
    }
}
