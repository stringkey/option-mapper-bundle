<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Tests;

use PHPUnit\Framework\TestCase;
//use Stringkey\OptionMapperBundle\StringkeyOptionMapperBundle;

class FunctionalBundleTest extends TestCase
{
    public function testServiceWiring()
    {
        $kernel = new OptionMapperTestingKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();

//        $optionMapper = $container->get('stringkey_option_mapper.option_mapper');
//
//        $this->assertInstanceOf(StringkeyOptionMapperBundle::class, $optionMapper);
//        $this->assertInternalType('string', $ipsum->getParagraphs());

    }
}