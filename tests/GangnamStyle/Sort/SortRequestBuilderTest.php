<?php

namespace BenTools\OpenCubes\Tests\GangnamStyle\Sort;

use BenTools\OpenCubes\Component\Sort\Sort;
use BenTools\OpenCubes\Component\Sort\SortComponent;
use BenTools\OpenCubes\GangnamStyle\Sort\SortRequestBuilder;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use function BenTools\UriFactory\Helper\uri;

class SortRequestBuilderTest extends TestCase
{

    public function testBuildRequestSort()
    {
        $component = new SortComponent([
            new Sort('foo'),
            new Sort('bar'),
        ]);

        $component->get('bar')->setAppliedDirection(Sort::SORT_DESC);

        $request = new Request('GET', uri('http://localhost/?foo=bar'));

        $requestBuilder = new SortRequestBuilder('o');
        $this->assertTrue($requestBuilder->supportsComponent($component));

        $request = $requestBuilder->buildRequest($component, $request);
        $expected = 'http://localhost/?foo=bar&o[foo]=asc&o[bar]=desc';
        $this->assertEquals($expected, urldecode((string) $request->getUri()));

    }
}
