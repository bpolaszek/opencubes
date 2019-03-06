<?php

namespace BenTools\OpenCubes\Tests\Component\BreakDown;

use BenTools\OpenCubes\Component\BreakDown\BreakDownComponent;
use BenTools\OpenCubes\Component\BreakDown\BreakDownComponentFactory;
use BenTools\OpenCubes\Component\BreakDown\BreakDownUriManager;
use BenTools\OpenCubes\Component\BreakDown\Model\Group;
use function BenTools\OpenCubes\stringify_uri;
use PHPUnit\Framework\TestCase;
use function BenTools\UriFactory\Helper\uri;

class BreakDownComponentFactoryTest extends TestCase
{

    /**
     * @test
     */
    public function it_can_create_an_empty_component()
    {
        $factory = new BreakDownComponentFactory();
        /** @var BreakDownComponent $component */
        $component = $factory->createComponent(uri(('/')));
        $this->assertInstanceOf(BreakDownComponent::class, $component);
        $this->assertEquals([], $component->all());
    }

    /**
     * @test
     */
    public function we_can_play_with_options()
    {
        $factory = new BreakDownComponentFactory([
            BreakDownComponentFactory::OPT_AVAILABLE_GROUPS => ['date', 'month']
        ]);

        /** @var BreakDownComponent $component */
        $component = $factory->createComponent(uri(('/')));
        $this->assertInstanceOf(BreakDownComponent::class, $component);
        $this->assertTrue($component->has('date'));
        $this->assertTrue($component->has('month'));
        $month = $component->get('month');
        $this->assertInstanceOf(Group::class, $month);
        $this->assertFalse($month->isApplied());

        $component = $factory->createComponent(uri(('/')), [
            $factory::OPT_DEFAULT_GROUPS => ['dayofweek'],
        ]);
        $this->assertTrue($component->has('date'));
        $this->assertTrue($component->has('month'));
        $this->assertTrue($component->has('dayofweek'));

        $component = $factory->createComponent(uri(('/?breakdown[]=device')), [
            $factory::OPT_DEFAULT_GROUPS => ['dayofweek'],
            $factory::OPT_AVAILABLE_GROUPS => ['browser'],
        ]);
        $this->assertFalse($component->has('date'));
        $this->assertFalse($component->has('month'));
        $this->assertFalse($component->has('dayofweek'));
        $this->assertTrue($component->has('browser'));
        $this->assertTrue($component->has('device'));

        $device = $component->get('device');
        $this->assertInstanceOf(Group::class, $device);
        $this->assertTrue($device->isApplied());

        $browser = $component->get('browser');
        $this->assertInstanceOf(Group::class, $browser);
        $this->assertFalse($browser->isApplied());
    }

    /**
     * @test
     */
    public function we_can_play_with_multigroup()
    {
        $factory = new BreakDownComponentFactory([], new BreakDownUriManager([BreakDownUriManager::OPT_BREAKDOWN_QUERY_PARAM => 'group_by']));
        $uri = uri('/?group_by[]=foo');
        /** @var BreakDownComponent $component */
        $component = $factory->createComponent($uri, [
            $factory::OPT_AVAILABLE_GROUPS => ['bar'],
        ]);

        $this->assertTrue($component->has('foo'));
        $this->assertTrue($component->has('bar'));

        $this->assertTrue($component->get('foo')->isApplied());
        $this->assertFalse($component->get('bar')->isApplied());

        $this->assertEquals('/?group_by[]=foo&group_by[]=bar', stringify_uri($component->get('bar')->getUri()));


        $component = $factory->createComponent($uri, [
            $factory::OPT_AVAILABLE_GROUPS => ['bar'],
            $factory::OPT_ENABLE_MULTIGROUP => false,
        ]);

        $this->assertTrue($component->has('foo'));
        $this->assertTrue($component->has('bar'));

        $this->assertTrue($component->get('foo')->isApplied());
        $this->assertFalse($component->get('bar')->isApplied());

        $this->assertEquals('/?group_by[]=bar', stringify_uri($component->get('bar')->getUri()));
    }
}
