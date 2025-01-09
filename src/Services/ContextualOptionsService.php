<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Stringkey\MetadataCoreBundle\Entity\Context;
use Stringkey\OptionMapperBundle\Entity\CustomOption;
use Stringkey\OptionMapperBundle\Entity\OptionGroup;
use Stringkey\OptionMapperBundle\Entity\OptionLink;
use Stringkey\OptionMapperBundle\Exception\IdenticalContextException;
use Stringkey\OptionMapperBundle\Exception\UnequalOptionGroupException;

class ContextualOptionsService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {

    }

    public static function constructOption(
        OptionGroup $optionGroup,
        Context     $context,
        string      $name,
        string      $externalReference,
        bool        $enabled = true,
    ): CustomOption {
        $customOption = new CustomOption();

        $customOption->setOptionGroup($optionGroup);
        $customOption->setContext($context);
        $customOption->setName($name);
        $customOption->setExternalReference($externalReference);
        $customOption->setEnabled($enabled);

        return $customOption;
    }

    public function create(OptionLink $optionLink, bool $flushNow = true): void
    {
        $this->entityManager->persist($optionLink);
        if ($flushNow) {
            $this->entityManager->flush();
        }
    }

    public static function constructOptionLink(
        CustomOption $sourceOption,
        CustomOption $targetOption,
        int $ordinality = 0
    ): OptionLink {

        // Check if the source and the target group are the same
        if ($sourceOption->getOptionGroup() !== $targetOption->getOptionGroup()) {
            throw new UnequalOptionGroupException("Source and target options need to be part of the same option group");
        }

        // Check if the source and the target contexts are different
        if ($sourceOption->getContext() === $targetOption->getContext()) {
            throw new IdenticalContextException("Source and target options must have different contexts");
        }

        $optionLink = new OptionLink();

        $optionLink->setSourceOption($sourceOption);
        $optionLink->setTargetOption($targetOption);
        $optionLink->setOrdinality($ordinality);

        return $optionLink;
    }
}