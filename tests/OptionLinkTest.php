<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Tests;

use PHPUnit\Framework\TestCase;
use Stringkey\MetadataCoreBundle\Entity\Context;
use Stringkey\OptionMapperBundle\Entity\OptionGroup;
use Stringkey\OptionMapperBundle\Exception\UnequalOptionGroupException;
use Stringkey\OptionMapperBundle\Services\ContextualOptionService;

class OptionLinkTest extends TestCase
{
    public function testThatOptionGroupsAreTheSame()
    {
        $optionGroup1 = new OptionGroup();
        $optionGroup2 = new OptionGroup();
        self::assertNotSame($optionGroup1, $optionGroup2);

        $context1 = new Context();
        $context1->setName('Context1');
        $context2 = new Context();
        $context2->setName('Context2');
        self::assertNotSame($context1, $context2);

        $contextualOption1 = ContextualOptionService::constructOption($optionGroup1, $context1, 'g1_co1_c1', 'g1_co1_c1');
        $contextualOption2 = ContextualOptionService::constructOption($optionGroup1, $context2, 'g1_co2_c2', 'g1_co2_c2');
//        $contextualOption3 = ContextualOptionService::constructOption($optionGroup1, $context1, 'g1_co3_c1', 'g1_co3_c1');
        $contextualOption3 = ContextualOptionService::constructOption($optionGroup2, $context1, 'g2_co4_c1', 'g2_co4_c1');

        $optionLink = ContextualOptionService::constructOptionLink($contextualOption1, $contextualOption2);

        $this->expectException(UnequalOptionGroupException::class);
        $optionLink = ContextualOptionService::constructOptionLink($contextualOption1, $contextualOption3);
    }
}