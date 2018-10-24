<?php

namespace BenTools\OpenCubes\Tests\Request;

use BenTools\OpenCubes\Component\Filter\FilterComponent;
use BenTools\OpenCubes\Component\Filter\SimpleFilter;
use BenTools\OpenCubes\Component\Sort\Sort;
use BenTools\OpenCubes\Component\Sort\SortComponent;
use BenTools\OpenCubes\GangnamStyle\Filter\FiltersRequestBuilder;
use BenTools\OpenCubes\GangnamStyle\Sort\SortRequestBuilder;
use BenTools\OpenCubes\Request\RequestBuilder;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class RequestBuilderTest extends TestCase
{

    public function testBuildRequest()
    {
        $components = [
            new FilterComponent([new SimpleFilter('status', 'active')]),
            new SortComponent([new Sort('createdBy', Sort::SORT_ASC)])
        ];

        $requestBuilder = new RequestBuilder([
            new FiltersRequestBuilder('filters'),
            new SortRequestBuilder('orderBy')
        ]);

        $request = new Request('GET', 'http://localhost/?foo=bar');
        $request = $requestBuilder->buildRequest($request, ...$components);

        $expected = 'http://localhost/?foo=bar&filters[status]=active&orderBy[createdBy]=asc';
        $this->assertEquals($expected, urldecode((string) $request->getUri()));
    }
}
