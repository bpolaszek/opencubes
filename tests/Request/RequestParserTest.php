<?php

namespace BenTools\OpenCubes\Tests\Request;

use BenTools\OpenCubes\Component\Filter\FilterComponent;
use BenTools\OpenCubes\Component\Filter\FilterComponentInterface;
use BenTools\OpenCubes\Component\Sort\Sort;
use BenTools\OpenCubes\Component\Sort\SortComponent;
use BenTools\OpenCubes\Component\Sort\SortComponentInterface;
use BenTools\OpenCubes\GangnamStyle\Filter\FiltersRequestParser;
use BenTools\OpenCubes\GangnamStyle\Sort\SortRequestParser;
use BenTools\OpenCubes\Request\RequestParser;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class RequestParserTest extends TestCase
{

    public function testGetComponents()
    {
        $sortComponent = new SortComponent([new Sort('foo', Sort::SORT_DESC)]);
        $uri = 'http://localhost/?foo=bar&f[status]=active&o[createdBy]=asc';
        $request = new Request('GET', $uri);
        $requestParser = new RequestParser([
            new FiltersRequestParser('f'),
            new SortRequestParser('o')
        ], [$sortComponent]);

        $components = iterable_to_array($requestParser->getComponents($request));
        $this->assertInstanceOf(FilterComponentInterface::class, $components[0]);
        $this->assertInstanceOf(SortComponentInterface::class, $components[1]);

        /** @var FilterComponent $filters */
        $filters = $components[0];
        $this->assertTrue($filters->hasFilter('status'));
        $this->assertEquals('active', $filters->getFilter('status')->getValue());

        /** @var SortComponent $sorts */
        $sorts = $components[1];
        $this->assertTrue($sorts->hasSort('createdBy'));
        $this->assertTrue($sorts->hasSort('foo'));
        $this->assertEquals([
            'foo'       => new Sort('foo', Sort::SORT_DESC),
            'createdBy' => new Sort('createdBy', Sort::SORT_ASC)
        ], $sorts->getSorts());
    }
}
