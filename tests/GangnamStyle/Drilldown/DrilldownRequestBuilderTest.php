<?php

namespace BenTools\OpenCubes\Tests\GangnamStyle\Drilldown;

use BenTools\OpenCubes\Component\Drilldown\Dimension;
use BenTools\OpenCubes\Component\Drilldown\DrilldownComponent;
use BenTools\OpenCubes\GangnamStyle\Drilldown\DrilldownRequestBuilder;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use function BenTools\UriFactory\Helper\uri;

class DrilldownRequestBuilderTest extends TestCase
{

    public function testBuildRequest()
    {
        $component = new DrilldownComponent([
            new Dimension('foo'),
            new Dimension('bar'),
        ]);

        $request = new Request('GET', uri('http://localhost/'));
        $requestBuilder = new DrilldownRequestBuilder('drill');
        $request = $requestBuilder->buildRequest($component, $request);
        $expected = 'http://localhost/?drill[]=foo&drill[]=bar';
        $this->assertEquals($expected, urldecode((string) $request->getUri()));

    }

    public function testSupportsComponent()
    {
        $component = new DrilldownComponent();
        $builder = new DrilldownRequestBuilder('d');
        $this->assertTrue($builder->supportsComponent($component));
    }
}
