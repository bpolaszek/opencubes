<?php

namespace BenTools\OpenCubes\Tests\GangnamStyle\Drilldown;

use BenTools\OpenCubes\Component\Drilldown\DrilldownComponent;
use BenTools\OpenCubes\GangnamStyle\Drilldown\DrilldownRequestParser;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Psr7\uri_for;

class DrilldownRequestParserTest extends TestCase
{

    public function testParseRequest()
    {
        $request = new Request('GET', uri_for('http://localhost/?foo=bar&drilldown[]=foo&drilldown[]=bar'));
        $parser = new DrilldownRequestParser('drilldown');

        $component = $parser->parseRequest($request);
        $this->assertCount(2, $component);
        $this->assertTrue($component->hasDimension('foo'));
        $this->assertTrue($component->hasDimension('bar'));
        $this->assertFalse($component->hasDimension('baz'));
    }

    public function testSupportsComponent()
    {
        $parser = new DrilldownRequestParser('d');
        $this->assertTrue($parser->supportsComponent(new DrilldownComponent()));
    }
}
