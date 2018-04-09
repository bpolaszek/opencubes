<?php

namespace BenTools\OpenCubes\Tests\GangnamStyle\Filter;

use BenTools\OpenCubes\Component\Filter\CompositeFilter;
use BenTools\OpenCubes\Component\Filter\FilterComponent;
use BenTools\OpenCubes\Component\Filter\RangeFilter;
use BenTools\OpenCubes\Component\Filter\SimpleFilter;
use BenTools\OpenCubes\GangnamStyle\Filter\FiltersRequestBuilder;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use function BenTools\UriFactory\Helper\uri;

class FiltersRequestBuilderTest extends TestCase
{

    public function testBuildRequest()
    {
        $component = new FilterComponent(...[
            new CompositeFilter('status', [
                new SimpleFilter('status', 'pending'),
                new SimpleFilter('status', 'reopened'),
                new SimpleFilter('status', 'awaiting'),
            ]),
            new SimpleFilter('assignee', 'me'),
            new RangeFilter('date', '2018-03-01', null)
        ]);

        $request = new Request('GET', uri('http://localhost/?foo=bar'));

        $requestBuilder = new FiltersRequestBuilder('f');
        $this->assertTrue($requestBuilder->supportsComponent($component));

        $request = $requestBuilder->buildRequest($component, $request);
        $expected = 'http://localhost/?foo=bar&f[status][]=pending&f[status][]=reopened&f[status][]=awaiting&f[assignee]=me&f[date]=[2018-03-01 TO *]';
        $this->assertEquals($expected, urldecode((string) $request->getUri()));

    }
}
